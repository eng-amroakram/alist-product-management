@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('edit Category') }}
@endsection
@csrf
@section('content')
    {{--Update Status--}}
    @include('dashboard.status.status')
    {{--simple error tracing--}}
    @include('dashboard.simple error tracing.simple_error_tracing')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$category->id)) }}
                <small>Created at{{ date_format($category->created_at, 'jS M Y') }} / Updated
                    at{{ date_format($category->updated_at, 'jS M Y') }}</small>
            </h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form method="POST" action="{{ route('dashboard.categories.update',$category->id) }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">
                <ul class="nav nav-tabs" id="myTab" role="tablist">
                    @foreach (Config::get('languages') as $lang => $language)
                        <li class="nav-item" role="presentation">
                            <a class="nav-link @if($loop->iteration===1) active @endif"
                               id="{{$language['display']}}-tab" data-toggle="tab"
                               href="#{{$language['display']}}" role="tab"
                               aria-controls="{{$language['display']}}"
                               aria-selected="true"> <span
                                    class="flag-icon flag-icon-{{$language['flag-icon']}}"></span> {{ $language['display'] }}
                            </a>
                        </li>
                    @endforeach
                </ul>
                <div class="tab-content" id="myTabContent">
                    @foreach (Config::get('languages') as $lang => $language)
                        <div class="tab-pane fade show @if($loop->iteration===1) active @endif"
                             id="{{$language['display']}}"
                             role="tabpanel" aria-labelledby="{{$language['display']}}-tab">

                            <!-- Category Name input -->
                            <div class="form-group">
                                <label for="inputSubServiceName">{{ __($language['display']) .' '.__('Name') }}</label>
                                <input name="{{$language['code']}}_name" type="text"
                                       class="form-control @error($language["code"].'_name') is-invalid @enderror"
                                       id="inputSubServiceName"
                                       placeholder="Enter Category name"
                                       value="{{ $category->getAttribute($language["code"].'_name') }}"
                                       @if($language['code']==='ar') dir="rtl" @endif>
                                @error($language["code"].'_name')
                                <span class="text-sm text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            <!-- Category Description input -->
                            <div class="form-group">
                                <label
                                    for="inputSubServiceDescription">{{ __($language['display']) .' '.__('Description') }}</label>
                                <input name="{{$language['code']}}_description" type="text"
                                       class="form-control @error($language["code"].'_description') is-invalid @enderror"
                                       id="inputSubServiceDescription"
                                       placeholder="Enter description"
                                       value="{{ $category->getAttribute($language["code"].'_description') }}"
                                       @if($language['code']==='ar') dir="rtl" @endif>
                                @error($language["code"].'_description')
                                <span class="text-sm text-danger">{{ $message }}</span>
                                @enderror
                            </div>

                            @if($loop->iteration===1)

                                <!-- Category Image input -->
                                <div class="form-group">
                                    <label for="inputSubServiceImage">{{ __('Category Primary Image') }}</label>
                                    <div class="input-group">
                                        <img alt="No Image" class="table-avatar img-thumbnail" width="30%" height="30%"
                                             id="sub-service-image-image-img-tag"
                                             src="{{ $category->image_url }}">
                                    </div>
                                    <div class="input-group">
                                        <div class="custom-file">
                                            <input name="image_path" type="file"
                                                   class="custom-file-input @error('image_path') is-invalid @enderror"
                                                   id="inputSubServiceImage" value="{{ $category->image_path }}">
                                            <label class="custom-file-label"
                                                   for="inputSubServiceImage">{{ __('Choose Category Image') }}</label>
                                        </div>
                                    </div>
                                    @error('image_path')
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Category Status input -->
                                <div class="form-group">
                                    <label for="InputStatus">{{ __('Category Status') }}</label>
                                    <div class="custom-control custom-switch custom-switch-on-green">
                                        <input name="status" type="checkbox" role="switch"
                                               class="custom-control-input @error('status') is-invalid @enderror"
                                               id="InputStatus" placeholder="Set Service Active"
                                               @if( (int)$category->status===1 ) checked @endif>
                                        <label class="custom-control-label" for="InputStatus">
                                            Toggle this switch to set category active
                                        </label>
                                    </div>
                                    @error('status')
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                                <!-- Category Order input -->
                                <div class="form-group">
                                    <label for="InputOrder">{{ __('Order') }}</label>
                                    <input name="order" type="number"
                                           class="form-control @error('order') is-invalid @enderror"
                                           id="InputOrder"
                                           placeholder="Order" value="{{ $category->order }}">
                                    @error('order')
                                    <span class="text-sm text-danger">{{ $message }}</span>
                                    @enderror
                                </div>

                            @endif

                        </div>

                    @endforeach
                </div>

            </div>
            <!-- /.card-body -->

            <div class=" card-footer">
                <a href="{{ route('dashboard.categories.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <input type="submit" value="Save Changes" class="btn btn-success float-right">
            </div>
        </form>
    </div>
@endsection
@section('js-script')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    $('#sub-service-image-image-img-tag').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#inputSubServiceImage").change(function () {
            readURL(this);
        });
    </script>
@endsection
