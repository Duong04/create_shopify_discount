@extends('layout')

@section('content')
<h3 class="px-4">REST Api</h3>
<ul class="nav">
    <a class="nav-link" href="{{route('rest')}}">REST</a>
    <a class="nav-link" href="{{route('graphql')}}">GraphQL</a>
</ul>
<div class="px-3">
    <a href="{{route('show.add')}}" class="btn btn-success">Add products</a>
    @if (session()->has('success'))
        <div class="alert alert-success">{{session('success')}}</div>
    @endif
    <form action="{{route('rest')}}" method="get" class="d-flex justify-content-end my-3">
        <select class="form-select" style="width:80px" name="limit" aria-label="Default select example">
            <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10</option>
            <option value="25" {{ $limit == 25 ? 'selected' : '' }}>25</option>
            <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50</option>
            <option value="100" {{ $limit == 100 ? 'selected' : '' }}>100</option>
        </select>
        <input name="filter" type="text" class="form-control w-25 mx-2" placeholder="Search">
        <button class="btn btn-success mx-2"><i class="bi bi-funnel"></i></button>
    </form>
    <a href="{{route('rest.rule')}}" class="nav-link text-success">Rule discounts</a>
    <form action="{{ route('rest.discount') }}" method="POST">
        <table class="table px-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Variants</th>
                    <th>Products</th>
                    <th>Vendor</th>
                    <th>Tags</th>
                    <th>price</th>
                    <th>compare_at_price</th>
                    <th>Status</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($products as $row)
                <tr>
                    <td>
                        <input onchange="selectAllVariants(this)" id="selectAllProducts" type="checkbox" name="selectedProducts[]">
                    </td>
                    <td class="d-flex" style="gap: 3px">
                        @foreach ($row['variants'] as $variant)
                            <div class="d-flex flex-column text-center">
                                <span>{{$variant['title']}}</span>
                                <input type="checkbox" class="variantCheckbox" name="selectedVariants[]" value="{{$variant['id']}}">
                            </div>
                        @endforeach
                    </td>
                    <td>
                        @if (isset($row['image']) && isset($row['image']['src']))
                            <img width="80px" src="{{ $row['image']['src'] }}" alt="{{ $row['image']['alt'] }}">
                        @endif
                        <span>{{$row['title']}}</span>
                    </td>
                    <td>{{$row['vendor']}}</td>
                    <td>{{$row['tags']}}</td>
                    <td>{{$row['variants'][0]['price']}}</td>
                    <td>{{$row['variants'][0]['compare_at_price']}}</td>
                    <td>{{$row['status']}}</td>
                    <td class="d-flex">
                        <a class="btn btn-warning mx-2" href="{{ route('show.edit', ['id' => $row['id']]) }}">Update</a>
                        <form action="{{route('delete', ['id' => $row['id']])}}" method="POST">
                            @method('DELETE')
                            <button class="btn btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
        <div class="row mb-3">
            <select name="rule_id" id="" class="form-control mx-2" style="width:180px;">
                <option value="">Rule discount</option> 
                @foreach ($ruleDiscounts as $rule)
                <option value="{{$rule['id']}}">{{$rule['name']}}</option> 
                @endforeach
            </select>
            <input class="form-control" style="width:180px;" type="text" name="nameRule" placeholder="Name discount">
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
            @if ($pagePrev)
                <li class="page-item"><a rel="previous" class="page-link" href="{{ route('rest', ['pageInfo' => $pagePrev, 'limit' => $limit, 'filter' => $filter]) }}"><i class="bi bi-chevron-left"></i></a></li>
            @else
                <li class="page-item disabled"><a class="page-link" href="#"><i class="bi bi-chevron-left"></i></a></li>
            @endif
            @if ($pageNext)
                <li class="page-item"><a rel="next" class="page-link" href="{{ route('rest', ['pageInfo' => $pageNext, 'limit' => $limit, 'filter' => $filter]) }}"><i class="bi bi-chevron-right"></i></a></li>
            @else
                <li class="page-item disabled"><a class="page-link" href="#"><i class="bi bi-chevron-right"></i></a></li>
            @endif
        </ul>
    </nav>
</div>
@endsection
