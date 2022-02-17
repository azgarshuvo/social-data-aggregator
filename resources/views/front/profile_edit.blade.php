@extends('layouts.master')
@section('titile', 'Dashboard')
@section('content')

<!-- Main content -->
<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
            <h1>Set Your Choices</h1>
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
            <h3 class="card-title">Edit Information</h3>
            </div>
            <!-- /.card-header -->
            <!-- form start -->
            <form method="post">
            @csrf
            <input type="hidden" name="formSubmitted" value="1">
            <div class="card-body">
                <div class="form-group">
                    <label for="exampleInputName">Name</label>
                    <input type="text" name="name" value="{{ $user_info->name }}" class="form-control" id="exampleInputName">
                </div>
                <div class="form-group">
                    <label for="exampleInputEmail1">Email address</label>
                    <input type="email" name="email" value="{{ $user_info->email }}" class="form-control" id="exampleInputEmail1" placeholder="Enter email">
                </div>
                <div class="form-group">
                  <label>Topics</label>
                  <select class="select2" name="topics[]" multiple="multiple" data-placeholder="Select a Topic" style="width: 100%;">
                    @php echo searchTopicOption2($user_info->topics) @endphp
                  </select>
                </div>
                <!-- /.form-group -->
                <div class="form-group">
                  <label>Location</label>
                  <select class="form-control select2" name="location" data-placeholder="Select a Location" style="width: 100%;">
                    @php echo locationOption($user_info->location) @endphp 
                  </select>
                </div>
                <!-- /.form-group -->
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
            </form>
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