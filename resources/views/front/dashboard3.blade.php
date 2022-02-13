@extends('layouts.master')
@section('titile', 'Dashboard')
@section('content')

<!-- Content Header (Page header) -->
<section class="content-header">
    <div class="container-fluid">
    <div class="row mb-2">
        <div class="col-sm-6">
        <h1>Social Posts</h1>
        </div>
        <div class="col-sm-6">
        <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item"><a href="#">Home</a></li>
            <li class="breadcrumb-item active">DataTables</li>
        </ol>
        </div>
    </div>
    </div><!-- /.container-fluid -->

    <div class="container-fluid">
        <h2 class="text-center display-6">Search</h2>
        <form method="get">
            <div class="row">
                <div class="col-md-10 offset-md-1">
                    <div class="row">
                        <div class="col-6">
                            <div class="form-group">
                                <label>Result Type:</label>
                                <select name="topics[]" class="select2" multiple="multiple" data-placeholder="Select Topic" style="width: 100%;">
                                    @php echo searchTopicOption($topics) @endphp
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>Platform:</label>
                                <select name= "data_from" class="select2" style="width: 100%;">
                                    @php echo platformOption($data_from) @endphp
                                </select>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="form-group">
                                <label>Location:</label>
                                <select name="location" class="select2" style="width: 100%;">
                                    @php echo locationOption($location) @endphp
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group input-group-lg">
                            <input type="search" class="form-control form-control-lg" placeholder="Type your keywords here" value="Lorem ipsum">
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-lg btn-default">
                                    <i class="fa fa-search"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

</section>

<!-- Main content -->
<section class="content">
    <div class="container-fluid">
    <div class="row">
        <div class="col-12">
        <div class="card">
            <div class="card-header">
            <h3 class="card-title">Results</h3>
            </div>
            <!-- /.card-header -->
            <div class="card-body">
            <table id="example1" class="table table-bordered table-striped">
            <thead>
                    <tr>
                        <th>Post Details</th>
                        <th>Likes</th>
                        <th>Comment</th>
                        <th>Location</th>
                    </tr>
                </thead>
                <tbody>
                    @if($social_post)
                    @foreach($social_post as $post)
                    <tr>
                        <td>{{ substr($post->post_details, 25) }}</td>
                        <td>{{ $post->like_count }}</td>
                        <td>{{ $post->comment_count }}</td>
                        <td>{{ $post->author_location }}</td>
                    </tr>
                    @endforeach
                    @else
                    <tr>
                      <td colspan="5">no result found!</td>
                    </tr>
                    @endif
                </tbody>
                <tfoot>
                    <tr>
                        <th>Post Details</th>
                        <th>Likes</th>
                        <th>Comment</th>
                        <th>Location</th>
                    </tr>
                </tfoot>
            </table>
            </div>
            <!-- /.card-body -->
        </div>
        <!-- /.card -->
        </div>
        <!-- /.col -->
    </div>
    <!-- /.row -->
    </div>
    <!-- /.container-fluid -->
</section>
<!-- /.content -->

@stop

@section('script')

<!-- Page specific script -->
<script>
  $(function () {
    $("#example1").DataTable({
      "responsive": true, "lengthChange": false, "autoWidth": false,
      "buttons": ["copy", "csv", "excel", "pdf", "print", "colvis"]
    }).buttons().container().appendTo('#example1_wrapper .col-md-6:eq(0)');
    $('#example2').DataTable({
      "paging": true,
      "lengthChange": false,
      "searching": false,
      "ordering": true,
      "info": true,
      "autoWidth": false,
      "responsive": true,
    });
  });
</script>

@endsection