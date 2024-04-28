<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RestDiscount;
use App\Models\RestRule;
use App\Models\GraphqlDiscount;
use App\Models\GraphqlRule;
use App\Models\User;
use App\Services\RestShopifyService;
use App\Services\GraphqlShopifyService;
use App\Http\Requests\DiscountRequest;
use App\Http\Requests\RestRuleRequest;
use App\Http\Requests\GraphqlRuleRequest;
use Illuminate\Support\Facades\Http;
use App\Jobs\ApplyRestDiscountJob;
use App\Jobs\ApplyGraphqlDiscountJob;
use App\Jobs\RestUpdateVariantPriceJob;
use App\Jobs\GraphqlUpdateVariantPriceJob;
use GuzzleHttp\Client;

class ShopifyController extends Controller
{

    protected $shopify;
    protected $RestShopifyService;
    protected $GraphqlShopifyService;

    public function __construct(Request $request)
    {
        $this->shopify = app('Shopify');
        $this->RestShopifyService = new RestShopifyService;
        $this->GraphqlShopifyService = new GraphqlShopifyService;
    }

    public function index(Request $request) {
        $shop = $request->query('shop');
        User::where('name', '!=', $shop)->update(['status' => 0]);
        User::where('name', $shop)->update(['status' => 1]);
        return redirect()->route('rest');
    }

    public function rest(Request $request)
    {
        $client = $this->RestShopifyService->getClient();
        $limit = $request->query('limit', 10);
        $filter = $request->query('filter');
        $page = $request->query('pageInfo');
        $ruleDiscounts = RestRule::get();
        $query = [
            'page_info' => $page,
            'limit' => $limit,
            'title' => $filter,
        ];
    
        $response = $client->get('products.json', ['query' => $query]);

        $responseBody = $response->getBody()->getContents();
        $data = json_decode($responseBody, true);
        $products = $data['products'];
        $pageNext = null;
        $pagePrev = null;

        if ($response->getHeader('link')) {
            $page_info = $response->getHeader('link')[0];
            preg_match('/<[^>]+page_info=([^>]+)>; rel="next"/', $page_info, $matches);
            if (isset($matches[1])) {
                $pageNext = $matches[1];
            }
            preg_match('/<[^>]+page_info=([^>]+)>; rel="previous"/', $page_info, $matches);
            if (isset($matches[1])) {
                $pagePrev = $matches[1];
            }
        }
        
        return view('rest.list', compact('products', 'pageNext', 'pagePrev', 'limit', 'filter', 'ruleDiscounts'));
    }

    public function restApplyDiscount(DiscountRequest $request)
    {
        $selectedVariants = $request->input('selectedVariants');
        $discountType = $request->input('discountType');
        $discountValue = $request->input('discountValue');
        $nameRule = $request->input('nameRule');
        $ruleId = $request->input('rule_id');

        if ($selectedVariants == null) {
            return redirect()->back();
        }
        
        if (!isset($ruleId)) {
            $request->validated();

            $rule = RestRule::create([
                'name' => $nameRule,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_status' => 'on'
            ]);
        }

        $id = isset($ruleId) ? $ruleId : $rule->id;

        foreach ($selectedVariants as $variant) {
            ApplyRestDiscountJob::dispatch($variant, $id)->onQueue('discounts');
        }
    
        return redirect()->back();

    }

    public function restRule() {
        $rules = RestRule::all();
        return view('rest.listRules', compact('rules'));
    }

    public function showEdit(string $id) {
        $rule = RestRule::find($id);
        return view('rest.updateRestRule', compact('rule'));
    }

    public function restUpdateRule(RestRuleRequest $request, $id) {
        $request->validated();
        
        $status = $request->input('discount_status');

        $data = $request->validated();

        $rule = RestRule::find($id);

        $rule->update($data);


        $rules = RestRule::join('rest_discounts', 'rest_rules.id','=','rest_discounts.rule_id')
            ->where('rest_rules.id', $id)
            ->get();
        foreach ($rules as $ruleItem) {
            RestUpdateVariantPriceJob::dispatch($status, $ruleItem)->onQueue('discounts');
        }

        RestRule::where('id', $id)->update(['discount_status' => $status]);
        return redirect()->route('rest.rule');
    }
    

    public function graphql(Request $request) {
        $client = $this->GraphqlShopifyService->getClient();
    
        $after = $request->input('after');
        $before = $request->input('before');
        $numProducts = intval($request->query('numProducts', 10));
        $filter = $request->query('filter', ''); 
        $status = isset($before) ? 'last' : 'first';
        $ruleDiscounts = GraphqlRule::get();

        $query = <<<QUERY
            query (\$numProducts: Int!, \$after: String, \$before: String, \$filter: String){
                products($status: \$numProducts, after: \$after, before: \$before, query: \$filter) {
                    nodes {
                        title
                        id
                        handle
                        vendor
                        status
                        tags
                        variants(first: 10) {
                            nodes {
                              title
                              id
                              price
                              compareAtPrice
                            }
                          }
                        featuredImage {
                            src
                            width
                            height
                        }
                    }
                    pageInfo {
                        hasNextPage
                        endCursor
                        hasPreviousPage
                        startCursor
                    }
                }
            }
        QUERY;
    
        $variables = [
            "numProducts" => $numProducts,
            "after" => $after,
            "before" => $before,
            "filter" => $filter
        ];
    
        $response = $client->query([
            "query" => $query,
            "variables" => $variables,
        ]);
    
        $responseBody = $response->getBody()->getContents();
        $responseData = json_decode($responseBody, true);
        $products = $responseData['data']['products']['nodes'];
        $pageInfo = $responseData['data']['products']['pageInfo'];
    
        return view('graphql.graphql', compact('products', 'pageInfo', 'numProducts', 'filter', 'ruleDiscounts'));
    }

    public function graphqlApplyDiscount(DiscountRequest $request)
    {

        $selectedVariants = $request->input('selectedVariants');
        $discountType = $request->input('discountType');
        $discountValue = $request->input('discountValue');
        $nameRule = $request->input('nameRule');
        $ruleId = $request->input('rule_id');

        if ($selectedVariants == null) {
            return redirect()->back();
        }

        if (!isset($ruleId)) {
            $request->validated();

            $rule = GraphqlRule::create([
                'name' => $nameRule,
                'discount_type' => $discountType,
                'discount_value' => $discountValue,
                'discount_status' => 'on'
            ]);
        }

        $id = isset($ruleId) ? $ruleId : $rule->id;

        foreach ($selectedVariants as $variant) {
            ApplyGraphqlDiscountJob::dispatch($variant, $id)->onQueue('discounts');
        }
    
        return redirect()->back();

    }

    public function graphqlRule() {
        $rules = GraphqlRule::all();
        return view('graphql.listRules', compact('rules'));
    }

    public function graphqlShowEdit(string $id) {
        $rule = GraphqlRule::find($id);
        return view('graphql.updateRestRule', compact('rule'));
    }

    public function graphqlUpdateRule(GraphqlRuleRequest $request, $id) {
        $request->validated();
        
        $status = $request->input('discount_status');

        $data = $request->validated();

        $rule = GraphqlRule::find($id);

        $rule->update($data);

        $rules = GraphqlRule::join('graphql_discounts', 'graphql_rules.id','=','graphql_discounts.rule_id')
            ->where('graphql_rules.id', $id)
            ->get();
        foreach ($rules as $rule) {
            GraphqlUpdateVariantPriceJob::dispatch($status, $rule)->onQueue('discounts');
        }
        GraphqlRule::where('id', $rule['id'])->update(['discount_status' => $status]);

        return redirect()->route('graphql.rule');
    }

    public function create() {
        return view('rest.add');
    }

    public function store(Request $request) {
        $validated = $request->validate([
            'title' => 'required',
            'vendor' => 'required',
        ]);

        $this->shopify->createProduct($validated);
        return redirect()->back()->with('success', 'Hello world');
    }

    public function edit($id) {
        $product = $this->shopify->getProduct($id);
        return view('rest.edit', compact('product'));
    }

    public function update(Request $request, string $id) {
        $validated = $request->validate([
            'title' => 'required',
            'vendor' => 'required',
        ]);

        $this->shopify->updateProduct($id, $validated);
        return redirect()->route('home')->with('success', 'Hello world');
    }

    public function destroy($id) {
        $this->shopify->deleteProduct($id);
        return redirect()->back()->with('success', 'Hello world');
    }
}
