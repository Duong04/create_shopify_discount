@extends('layout')

@section('content')
<h3 class="px-4">GraphQL Api</h3>
<ul class="nav">
    <a class="nav-link" href="{{route('rest')}}">REST</a>
    <a class="nav-link" href="{{route('graphql')}}">GraphQL</a>
</ul>
<div class="px-3">
    <form action="{{route('graphql')}}" method="get" class="d-flex justify-content-end my-3">
        <select class="form-select" style="width:80px" name="numProducts" aria-label="Default select example">
            <option value="10" {{ $numProducts == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ $numProducts == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ $numProducts == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ $numProducts == 100 ? 'selected' : '' }}>100</option>
        </select>
        <input name="filter" type="text" class="form-control w-25 mx-2" placeholder="Search">
        <button class="btn btn-success mx-2"><i class="bi bi-funnel"></i></button>
    </form>
    <a href="{{route('graphql.rule')}}" class="nav-link text-success">Rule discounts</a>
    <form action="{{route('graphql.discount')}}" method="POST">
        <table class="table px-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Variants</th>
                    <th>Products</th>
                    <th>Vendor</th>
                    <th>Tag</th>
                    <th>Price</th>
                    <th>Compare at price</th>
                    <th>Handle</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $row)
                <tr>
                    <td>
                        <input onchange="selectAllVariants(this)" id="selectAllProducts" type="checkbox" name="selectedProducts[]">
                    </td>
                    <td class="d-flex" style="gap: 3px">
                        @foreach ($row['variants']['nodes'] as $variant)
                            <div class="d-flex flex-column text-center">
                                <span>{{$variant['title']}}</span>
                                <input type="checkbox" class="variantCheckbox" name="selectedVariants[]" value="{{$variant['id']}}">
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @if (isset($row['featuredImage']))
                            <img width="80px" height="80px" src="{{$row['featuredImage']['src']}}" alt="">
                        @endif
                        <span>{{$row['title']}}</span>
                    </td>
                    <td>{{$row['vendor']}}</td>
                    <td>
                        @if(isset($row['tags'][0]))
                            {{ $row['tags'][0] }}
                        @else
                            No tags
                        @endif
                    </td>
                    <td>{{$row['variants']['nodes'][0]['price']}}</td>
                    <td>{{$row['variants']['nodes'][0]['compareAtPrice']}}</td>
                    <td>{{$row['handle']}}</td>
                    <td>{{$row['status']}}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row mb-3">
            <select name="rule_id" id="" class="form-control" style="width:180px;">
                <option value="">Rule discount</option> 
                @foreach ($ruleDiscounts as $rule)
                <option value="{{$rule['id']}}">{{$rule['name']}}</option> 
                @endforeach
            </select>
            <input class="form-control mx-2" style="width:180px;" type="text" name="nameRule" placeholder="Name discount">
            <select style="width: 180px" class="form-control mx-2" name="discountType" id="discountType">
                <option value="percentage">Percentage</option>
                <option value="fixed_amount">Fixed Amount</option>
            </select>
            <input style="width: 180px" class="form-control" type="number" name="discountValue" placeholder="Discount value">
            <button class="btn btn-warning mx-2" style="width: 100px;">Apply</button>
        </div>
        @method('PUT')
    </form>
    <nav aria-label="Page navigation example">
        <ul class="pagination d-flex justify-content-center">
            @if (isset($pageInfo['hasPreviousPage']) && $pageInfo['hasPreviousPage'])
                <li class="page-item"><a class="page-link" href="{{ route('graphql', ['before' => $pageInfo['startCursor'], 'numProducts' => $numProducts, 'filter' => $filter]) }}"><i class="bi bi-chevron-left"></i></a></li>
            @else
                <li class="page-item"><a class="page-link disabled" href="#"><i class="bi bi-chevron-left"></i></a></li>
            @endif
            @if (isset($pageInfo['hasNextPage']) && $pageInfo['hasNextPage'])
                <li class="page-item"><a class="page-link" href="{{ route('graphql', ['after' => $pageInfo['endCursor'], 'numProducts' => $numProducts, 'filter' => $filter]) }}"><i class="bi bi-chevron-right"></i></a></li>
            @else
                <li class="page-item"><a class="page-link disabled" href="#"><i class="bi bi-chevron-right"></i></a></li>
            @endif
        </ul>
    </nav>
</div>
@endsection

