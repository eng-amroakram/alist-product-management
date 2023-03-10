@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('view category') }}
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$category->id)) }}</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <dl class="card-body row">
                <dt class="col-sm-3">{{ __('Category ID') }}</dt>
                <dd class="col-sm-9">{{ $category->id }}</dd>

                <dt class="col-sm-3">{{ __('Category Image') }}</dt>
                <dd class="col-sm-9">
                    <img class="img-thumbnail" src="{{ $category->image_url }}"
                         alt="No Image" width="80" height="80">
                </dd>

                @foreach (Config::get('languages') as $lang => $language)
                    <dt class="col-sm-3">{{ __($language['display']) .' '.__('Category name') }}</dt>
                    <dd class="col-sm-9">{{ $category->getAttribute($language["code"].'_name') }}</dd>

                    <dt class="col-sm-3">{{ __($language['display']) .' '.__('Category description') }}</dt>
                    <dd class="col-sm-9">{{ $category->getAttribute($language["code"].'_description') }}</dd>
                @endforeach


                <dt class="col-sm-3">{{ __('Category status') }}</dt>
                <dd class="col-sm-9">{{ $category->status===1 ? 'Active' : 'Inactive'  }}</dd>

                <dt class="col-sm-3">{{ __('Category order queue') }}</dt>
                <dd class="col-sm-9">{{ $category->order }}</dd>

                @if($count_products>0)
                    @foreach (Config::get('languages') as $lang => $language)
                        @foreach($products as $product)
                            <dt class="col-sm-3 bg-danger">{{ __($language['display']) .' '.__('Product Title') }}</dt>
                            <dd class="col-sm-9 text-danger">{{ $product->getAttribute($language['code'].'_title') }}</dd>
                            <dt class="col-sm-3 bg-danger">{{ __($language['display']) .' '.__('Product Link') }}</dt>
                            <dd class="col-sm-9 text-danger">
                                <a class="text-danger"
                                   href="{{ route('dashboard.products.show', $product)}} ">{{route('dashboard.products.show', $product)}}</a>
                            </dd>
                        @endforeach
                    @endforeach
                @else
                    <dt class="col-sm-3">{{ __('Category Products') }}</dt>
                    <dd class="col-sm-9">{{ $products }}</dd>
                @endif


                <dt class="col-sm-3">{{ __('Created at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($category->created_at)) }}</dd>

                <dt class="col-sm-3">{{ __('Updated at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($category->updated_at)) }}</dd>
            </dl>
        </div>
    </div>
@endsection
