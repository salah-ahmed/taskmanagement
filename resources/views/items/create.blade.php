@extends('layouts.master')

@section('content')
    <div class="col-sm-8">
        <form action="{{route('items.store')}}" method="POST" class="mt-3">
            @csrf
            <div class="form-group">
                <label for="description">Description</label>
                <input type="text" class="form-control" name="description"
                    id="description" placeholder="Item Description.">
            </div>

            <input type="hidden" name="list" value="{{$list}}">
            <button type="submit" class="btn btn-primary">save</button>
        </form>
        <br>
        @include('errors.errors')
    </div>
@endsection