@extends('layout')
@section('content')

    <h3 class="p-4">Rest Api</h3>
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    @if (session()->has('success'))
        <div class="alert alert-success">{{session('success')}}</div>
    @endif
    <form class="w-25 px-3" method="POST" action="{{route('show.store')}}">
        @csrf
        <div class="mb-3">
            <label for="" class="form-label">Title</label>
            <input value="{{old('title')}}" type="text" class="form-control" name="title" id="title" placeholder="Title">
        </div>
        <div class="mb-3">
            <label for="" class="form-label">Vendor</label>
            <input value="{{old('vendor')}}" type="text" class="form-control" name="vendor" id="vendor" placeholder="Vendor">
        </div>
        <button class="btn btn-success">Create</button>
    </form>
@endsection