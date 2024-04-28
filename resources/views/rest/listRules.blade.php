@extends('layout')
@section('content')
    <h3 class="px-4">REST Rules</h3>
    <ul class="nav">
        <a class="nav-link" href="{{route('rest')}}">REST</a>
        <a class="nav-link" href="{{route('graphql')}}">GraphQL</a>
    </ul>
    <div class="px-3">
        <table class="table px-3">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Discount type</th>
                    <th>Discount value</th>
                    <th>Discount status</th>
                    <th>action</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($rules as $rule)
                <tr>
                    <th>{{$rule['id']}}</th>
                    <td>{{$rule['name']}}</td>
                    <td>{{$rule['discount_type']}}</td>
                    <td>{{$rule['discount_value']}}</td>
                    <td>{{$rule['discount_status']}}</td>
                    <td>
                        <a class="btn btn-warning mx-2" href="{{route('rest.show.edit', ['id' => $rule['id']])}}">Update</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@endsection
