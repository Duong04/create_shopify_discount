@extends('layout')
@section('content')
    <h3 class="px-4">Update Rule</h3>
    <ul class="nav">
        <a class="nav-link" href="{{route('rest.rule')}}">Change</a>
    </ul>
    <div class="px-3">
        <form method="POST" action="{{route('graphql.handle.edit', ['id' => $rule['id']])}}" class="w-25 mx-auto">
            @method('PUT')
            <div class="mb-3">
                <label for="" class="form-label">Name</label>
                <input name="name" type="text" class="form-control" placeholder="Name" value="{{$rule['name']}}">
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Discount type</label>
                <select class="form-control" name="discount_type" id="discountType">
                    <option {{$rule['discount_type'] == 'percentage' ? 'selected' : ''}} value="percentage">Percentage</option>
                    <option {{$rule['discount_type'] == 'fixed_amount' ? 'selected' : ''}} value="fixed_amount">Fixed Amount</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="" class="form-label">Discount value</label>
                <input name="discount_value" type="text" class="form-control" placeholder="Discount value" value="{{$rule['discount_value']}}">
            </div>
            <div class="mb-3 d-flex" style="gap: 20px">
                <div>
                    <label for="" class="form-label">on</label>
                    <input {{$rule['discount_status'] == 'on' ? 'checked' : ''}} class="d-block" type="radio" name="discount_status" value="on">
                </div>
                <div>
                    <label for="" class="form-label">off</label>
                    <input {{$rule['discount_status'] == 'off' ? 'checked' : ''}} class="d-block" type="radio" name="discount_status" value="off">
                </div>
            </div>
            <button class="btn btn-primary">Update</button>
        </form>
    </div>
@endsection
