@extends('layouts.master')
@section('titile', 'Dashboard')
@section('content')

<!-- Main content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h5>Message</h5>
        </div>
    </div>
    </div><!-- /.container-fluid -->
</section>

<section class="content">
    <div class="container-fluid">
    <div class="row">
        <!-- left column -->
        <div class="col-md-6">
        <!-- general form elements -->
        <div class="card card-primary">
            <div class="card-header">
            <h3 class="card-title">{{$message}}</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
        </div>
        <!-- /.card -->
        </div>
        <!--/.col (right) -->
    </div>
    <!-- /.row -->
    </div><!-- /.container-fluid -->
</section>
<!-- /.content -->

@stop