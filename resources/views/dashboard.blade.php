@extends('layouts.app')

@section('header', 'System Overview')

@section('content')
<div class="row">
    <div class="col-md-4">
        <div class="card bg-primary text-white mb-4">
            <div class="card-body">
                <h5>Total Employees</h5>
            </div>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card bg-success text-white mb-4">
            <div class="card-body">
                <h5>Active Permanent</h5>
            </div>
        </div>
    </div>
</div>
@endsection