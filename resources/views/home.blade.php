@extends('layouts.app')

@section('content')

    <div class="row">
    <section layout="row" flex ng-controller="PostCtrl as postCtrl">
        <md-content flex layout-padding>
            <div layout="column" layout-fill>
                @include('html.post-create',compact('categories'))

                <span class="md-title">
                    @{{ postCtrl.feedName }}
                </span>

                <a class="btn btn-success-outline"
                   ng-if="postCtrl.slug"
                   ng-click="postCtrl.followCate(postCtrl.slug)">
                    @{{ postCtrl.postFeedFollow }}
                </a>

                <div id="homeFeed">
                    @include('html.feed-list',compact('feeds'))
                </div>
            </div>
        </md-content>
        <md-sidenav class="md-sidenav-right md-component-id="right" md-is-locked-open="$mdMedia('gt-sm')">
            <div class="media panel md-padding">
                <div class="media-body">
                    {{--{{ Auth::user() }}--}}
                    <h4 class="media-heading">
                        <a href="/{!! Auth::user()->displayname !!}">
                        {{ Auth::user()->firstname }}
                        </a>
                    </h4>
                    {{ Auth::user()->description }}
                    <div>
                        <b> {{ Auth::user()->posts }}</b> posts
                        <b> {{ Auth::user()->followers }}</b> followers
                        <b> {{ Auth::user()->following }}</b> following
                    </div>
                </div>
                <div class="media-right">
                    <a href="#">
                        <img class="media-object"
                             width="80px"
                             src="{{ Auth::user()->profile_img }}"
                             alt="...">
                    </a>
                </div>
            </div>

            <ul class="nav nav-pills">
                @foreach ($categories as $cate)
                    <li class="nav-item btn-success-outline"
                        role="presentation">
                        <a href="#" class="btn btn-success-outline"
                           ng-click="postCtrl.getFeedCate('{{ $cate->slug }}','{{$cate->name}}');
                                    postCtrl.feedFollowStatus('{{ $cate->slug }}')">
                            {{$cate->name}}</a>
                    </li>
                @endforeach
            </ul>
        </md-sidenav>
    </section>
    </div>
@endsection
