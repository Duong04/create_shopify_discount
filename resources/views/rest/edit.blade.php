@extends('layout')
@section('content')

    <h3 class="p-4">Rest Api</h3>
    <form class="w-25 px-3" action="{{route('show.update', ['id'=>$product['id']])}}" method="POST">
        @method('PUT')
        @csrf
        <div class="mb-3">
            <label for="" class="form-label">Title</label>
            <input value="{{$product['title']}}" type="text" class="form-control" name="title" id="title" placeholder="">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Vendor</label>
            <input value="{{$product['vendor']}}" type="text" class="form-control" name="vendor" id="vendor" placeholder="">
        </div>
        <button class="btn btn-success">Update</button>
    </form>
@endsection
