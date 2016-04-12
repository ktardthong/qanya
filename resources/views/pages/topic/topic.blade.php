@extends('layouts.app')

@section('content')

<div layout="column" layout-align="center">

    <md-content ng-controller="PostCtrl as postCtrl">

        <div class="layoutSingleColumn md-padding">

            <script>

                //track pages in most views
                var ref = new Firebase("https://qanya.firebaseio.com/mostviews/{{$uuid}}/view");
                ref.transaction(function (current_value) {
                    return (current_value || 0) + 1;
                })


                {{-- Increment page view --}}
                var ref = new Firebase("https://qanya.firebaseio.com/topic/{{$uuid}}/view");
                ref.transaction(function (current_value) {
                    return (current_value || 0) + 1;
                })

                @if(Auth::user())
                    //Put this in user's history
                    var ref = new Firebase("https://qanya.firebaseio.com/user/{{Auth::user()->uuid}}/history");
                    ref.once("value", function(snapshot) {
                        ref.child('{{$uuid}}').set(moment().format());
                    })
                @endif
            </script>




            <div class="container-fluid">

                <div class="pull-left">
                    <span>
                        <i class="fa fa-clock-o fa-x"></i>
                        {!! Carbon\Carbon::parse($topic_created_at)->diffForHumans() !!}
                    </span>
                </div>

                View: {{ $views }}

                {{-- FAB buttons --}}
                <div class="pull-right">
                @if($is_user)
                    <md-fab-toolbar md-open="false" count="0" md-direction="left">

                        <md-fab-trigger class="align-with-text">
                            <md-button aria-label="edit" class="md-fab md-mini md-primary"
                                       ng-click="postCtrl.editable='true';"
                                       onclick='$("#topicContent").addClass("editBorder");'>
                                    <md-tooltip md-direction="bottom">
                                        Edit
                                    </md-tooltip>
                                <md-icon md-svg-src="/assets/icons/ic_mode_edit_white_24px.svg">></md-icon>
                            </md-button>
                        </md-fab-trigger>
                        <md-toolbar>
                            <md-fab-actions class="md-toolbar-tools">
                                <md-button aria-label="save" class="md-icon-button"
                                           ng-click="postCtrl.updateTopicContent('{{$uuid}}','{{$topic_id}}')"
                                           onclick='$("#topicContent").removeClass("editBorder");'>
                                    <md-icon md-svg-src="/assets/icons/ic_save_white_24px.svg"></md-icon>
                                </md-button>

                                <div flow-init
                                     flow-name="uploader.flow"
                                     flow-files-added="postCtrl.processFiles($files,'#topicContent')">
                                    <md-button aria-label="photo" class="md-icon-button"
                                               flow-btn type="file" name="image">
                                        <md-icon md-svg-src="/assets/icons/ic_insert_photo_white_24px.svg"></md-icon>
                                    </md-button>
                                </div>

                                <md-button aria-label="delete" class="md-icon-button"
                                           ng-click="postCtrl.showConfirmDelete($event,'{{$uuid}}','{{Auth::user()->uuid}}')" >
                                    <md-icon md-svg-src="/assets/icons/ic_delete_white_24px.svg"></md-icon>
                                </md-button>
                            </md-fab-actions>
                        </md-toolbar>
                    </md-fab-toolbar>
                @endif
                </div>
            </div>


            <h1 class="md-display-1">
                {!! HTML::decode($title) !!}
            </h1>

            <span ng-if="{{ $topic_type }} == 2;"
                  ng-init="postCtrl.getReview('{{$uuid}}')"
                  class="pull-right">
                <review-topic data="postCtrl.responseReview<?=$uuid?>"></review-topic>
            </span>

            <div class      =   "reading img-fluid"
                 id         =   "topicContent"
                 ng-init    =   "postCtrl.editable='false'"
                 contenteditable="@{{ postCtrl.editable }}">
                {!! nl2br($body) !!}
            </div>


            @if($topic_location)
                <post-location ng-init="postCtrl.getPostedLocation('{{ $topic_location }}')"
                               data="postCtrl.locationDetail"></post-location>
            @endif


            {{-- If it has been edited--}}
            @if($is_edited)
                @{{ 'KEY_EDITED' | translate }}
                - {!! Carbon\Carbon::parse($topic_updated_at)->diffForHumans() !!}
            @endif


            {{-- Tag list --}}
            <div>
                @if(!empty($tags))
                    @foreach($tags as $tag)
                        <a href="/tag/{{$tag}}">#{{$tag}}</a>
                    @endforeach
                @endif
            </div>


            {{-- Tally and share --}}
            <div class="container-fluid md-margin">
                <div>
                    <h5>
                        @include('html.topic-tally',compact('topics_uid','uuid'))
                    </h5>
                </div>

                {{-- Share button --}}
                <div>
                    {{-- Facebook--}}
                    <div class="fb-share-button"
                         data-href="{{ Request::url() }}"
                         data-layout="icon_link"></div>
                    <span flex></span>
                    {{-- Twitter--}}
                    <div>
                        <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                        <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                    </div>
                </div>
                {{-- end share --}}
            </div>

            <md-divider></md-divider>

            
            {{-- Author section --}}
            <div class="media md-margin" ng-init="profileCtrl.getUserStat('{{$topics_uid}}')">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object"
                             width="60px"
                             src="{{ $poster_img }}"
                             alt="...">
                    </a>
                </div>
                <div class="media-body">
                    <h4 class="md-title">
                        <a href="/{{ $username }}">
                            {{ $user_fname }}
                        </a>
                    </h4>
                    <div class="md-subhead">
                        {{ $user_descs }}
                    </div>
                    <div>
                        <b> {{ profileCtrl.user_stat_<?=str_replace('-','',$topics_uid)?>.upvote }}</b>
                        @{{ 'KEY_UPVOTE' | translate }}
                    </div>
                </div>

                <!-- Follow Button -->

                <div class="media-right">
                    @if(is_null($is_user))

                        <button class="btn btn-success-outline"
                                @if(Auth::check())
                                    ng-init ="postCtrl.isFollow('{!! Auth::user()->uuid !!}', '{!! $topics_uid !!}')"
                                    ng-click="postCtrl.followUser('{!! Auth::user()->uuid !!}', '{!! $topics_uid !!}')"
                                @endif>
                            @{{ postCtrl.postFollow }}
                        </button>

                    @endif
                </div>

            </div>
            
        </div>



<div style="background: #fafafa;">

    <div class="layoutSingleColumn md-padding"
         ng-controller="PostCtrl as postCtrl"
         ng-init="postCtrl.getReplies('{{$uuid}}')" >

        <div>
            <md-icon md-svg-icon="/assets/icons/ic_comment_black_24px.svg"></md-icon>
            @{{ 'KEY_COMMENTS' | translate }}
            @{{ postCtrl.replyList.length | groupBy: 'topic_id' }}
        </div>


        {{-- Reply Section--}}
        @if (Auth::user())
            <md-content layout-padding layout="row"  layout-align="start end">
                <md-list flex>
                    <md-list-item class="md-3-line">
                        <img class="md-avatar"
                             src="{!! Auth::user()->profile_img !!}"
                             alt="{!! Auth::user()->displayname !!}">
                        <div class="md-list-item-text" layout="column">
                            <form ng-submit="postCtrl.postReply('{{$uuid}}','{{$topics_uid}}','{{Auth::user()->uuid }}')">
                                <md-content layout-padding layout="row"  layout-align="start center">
                                    <div flex>
                                        <md-input-container class="md-block">
                                            <label>@{{ 'KEY_WRITE_REPLY' | translate }}</label>
                                            <textarea ng-model="postCtrl.topicReply"
                                                      md-maxlength="150" rows="5"
                                                      md-select-on-focus></textarea>
                                        </md-input-container>
                                    </div>

                                    <div>
                                        <md-button  type="submit"
                                                    ng-disabled="false"
                                                    class="md-fab md-mini md-primary"
                                                    aria-label="@{{ 'KEY_POST' | translate }}">
                                            <md-tooltip md-direction="bottom">
                                                @{{ 'KEY_POST' | translate }}
                                            </md-tooltip>
                                            <md-icon md-svg-icon="/assets/icons/ic_send_white_24px.svg"></md-icon>
                                        </md-button>
                                    </div>
                                </md-content>

                                {{-- If there is review then allow user to rate here--}}
                                <div ng-if="{{ $topic_type }} == 2" ng-init="postCtrl.getReview ('{{$uuid}}')">
                                    <review-form ng-model="postCtrl.reviewForm"
                                                 data="postCtrl.responseReview{{$uuid}}"></review-form>
                                </div>

                            </form>
                        </div>


                    </md-list-item>
                </md-list>
            </md-content>
        @else
            <div class="media md-margin">
                <div class="media-body">
                    <h4 class="media-heading">
                        @{{ 'KEY_WRT_COMMENT' | translate }}
                    </h4>
                </div>
            </div>
        @endif

            {{-- Appending new reply--}}
            <md-list id="reply_append_{{$uuid}}"></md-list>

            <md-content>
                    <md-list flex ng-repeat="(key, value) in postCtrl.replyList  | groupBy: 'topic_id' ">
                        <md-list-item>
                            #@{{ $index + 1 }} -
                            <a href="/@{{ value[0].displayname }}" target="_blank">
                                @{{ value[0].firstname }}
                            </a>
                        </md-list-item>
                        <md-list-item class="md-3-line" id="reply_message">
                            <img ng-src="@{{ value[0].profile_img }}"
                                 class="md-avatar" alt="@{{ value[0].firstname }}" />
                            <div class="md-list-item-text" layout="column">
                                <h3>
                                    <span class="md-body-2"> @{{ value[0].body | htmlToPlaintext }}</span>
                                </h3>
                                <small>
                                    <span am-time-ago="value[0].created_at | amParse:'YYYY-MM-DD H:i:s'"></span>
                                </small>
                            </div>

                            <div layout="row" layout-align="end" ng-if="postCtrl.avgScore(value) != 'NaN'">
                                <span class="md-title">
                                    @{{ postCtrl.avgScore(value) }}
                                </span>
                                <div ng-repeat="review in value">
                                    <p>
                                        @{{ review.criteria }} - @{{ review.scores }}
                                    </p>
                                </div>
                            </div>

                            {{--<div layout="row" layout-align="end center">
                                <md-button class="md-secondary md-icon-button" aria-label="call">
                                    <md-icon md-svg-icon="/assets/icons/ic_reply_black_24px.svg"></md-icon>
                                </md-button>
                            </div>--}}

                            <md-divider></md-divider>
                        </md-list-item>

                </md-list>
            </md-content>
        </div>
</div>
    </md-content>
</div>

@endsection