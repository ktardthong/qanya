@extends('layouts.app')

@section('content')

<div class="row">
    <md-content ng-controller="PostCtrl as postCtrl"
                class="md-padding">

            <div class="layoutSingleColumn">
                {{$is_user}}
                <script>
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

                <span>
                    <i class="fa fa-clock-o fa-x"></i>{{ $created_at }}
                </span>

                @if($is_user)
                    <span class="pull-right"
                          ng-click="postCtrl.updateTopicContent('{{$uuid}}','{{$topic_id}}')">
                          Remove
                    </span>

                    <span class="pull-right" ng-click="postCtrl.editable='true';"
                    onclick="$('#topicContent').css('background-color', '#FFFFA5');">edit</span>

                    <span class="pull-right"
                          ng-click="postCtrl.updateTopicContent('{{$uuid}}','{{$topic_id}}')">
                          save
                    </span>
                @endif

                <h1 class="md-display-1">
                    {!! HTML::decode($title) !!}
                </h1>


                <div class="reading img-fluid" id="topicContent"
                     ng-init    =   "postCtrl.editable='false'"
                     contenteditable="@{{ postCtrl.editable }}">
                    {!! nl2br($body) !!}
                </div>

                {{-- Tag list --}}
                <div>
                    @if(!empty($tags))
                        @foreach($tags as $tag)
                            <a href="/tag/{{$tag}}">#{{$tag}}</a>
                        @endforeach
                    @endif
                </div>

                {{-- Tally and share --}}
                <div class="container-fluid">
                    <div class="pull-left">
                        <h5>
                            @include('html.topic-tally',compact('topics_uid','uuid'))
                        </h5>
                    </div>

                    <div class="pull-right">
                        {{-- Share button --}}
                        <div class="fb-share-button"
                             data-href="https://developers.facebook.com/docs/plugins/"
                             data-layout="icon_link"></div>

                        <span>
                            <script type="text/javascript" src="//media.line.me/js/line-button.js?v=20140411" ></script>
                            <script type="text/javascript">
                                new media_line_me.LineButton({"pc":false,"lang":"en","type":"a"});
                            </script>
                        </span>
                        <div>
                            <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                        </div>
                    </div>
                    {{-- end share --}}
                </div>

            

                {{-- Author section --}}
                <div class="media md-margin md-card">
                    <div class="media-left">
                        <a href="#">
                            <img class="media-object"
                                 width="60px"
                                 src="{!! $poster_img !!}"
                                 alt="...">
                        </a>
                    </div>
                    <div class="media-body">
                        <h4 class="media-heading">
                            <a href="/{{ $username }}">
                                {{ $user_fname }}
                            </a>
                        </h4>
                        {{ $user_descs }}
                        <div>
                            <b>10</b> post
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
    </md-content>
</div>


<div class="layoutSingleColumn" ng-controller="PostCtrl as postCtrl"  ng-init="postCtrl.replyList = postCtrl.getReplies('{{$uuid}}')" >

    @if (Auth::user())
        <form ng-submit="postCtrl.postReply('{{$uuid}}','{{$topics_uid}}','{{Auth::user()->uuid }}')">
            <div class="media md-margin">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object"
                             width="60px"
                             src="{!! Auth::user()->profile_img !!}"
                             alt="...">
                    </a>
                </div>
                <div class="media-body">
                    <h4 class="media-heading">
                        You
                    </h4>
                    <div contenteditable="true"
                         placeholder="Any comments?"
                         class="panel card"
                         data-content="test"
                         id="topicReplyContainer">
                    </div>
                    <md-button type="submit"
                               class="md-raised md-primary">Submit</md-button>
                </div>
            </div>
        </form>
    @else
        <div class="media md-margin">
            <div class="media-body">
                <h4 class="media-heading">
                    Write a response
                </h4>
            </div>
        </div>
    @endif

        {{-- Appending new reply--}}
        <md-list id="reply_append_{{$uuid}}"></md-list>


        @for($i=0;$i<count($topic_replies);$i++)

            <div class="media md-margin" id="reply_message_<?=$i?>">
                <div class="media-left">
                    <a href="#">
                        <img class="media-object"
                             width="60px"
                             src="{!! $topic_replies[$i]->profile_img !!}"
                             alt="...">
                    </a>
                </div>
                <div class="media-body">
                    <h5 class="media-heading">
                        <a href="/{{ $topic_replies[$i]->displayname }}" target="_blank">
                            {{ $topic_replies[$i]->firstname }}
                        </a>
                        <small> -
                            <span am-time-ago="'{!! $topic_replies[$i]->replycreated_at !!}' | amParse:'YYYY-MM-DD HH:mm:ss'"></span>
                        </small>
                    </h5>

                    <div class="card-block">

                        {!! HTML::decode($topic_replies[$i]->body) !!}

                        <p>
                            <a href="#reply_message_<?=$i?>" class="card-link"
                                @if(Auth::check())
                                    ng-click="postCtrl.replyInReplyUpvote('{{$topic_replies[$i]->id}}',
                                                                          '{{ $uuid }}',
                                                                          '{{$topic_replies[$i]->uuid}}',
                                                                          '{{Auth::user()->uuid}}')"
                                @endif
                                ng-init="postCtrl.replyInReplyUpvoteTally('{{$topic_replies[$i]->id}}')">
                                <i class="fa fa-chevron-up"></i>
                                {{ postCtrl.reply_upvote_<?= $topic_replies[$i]->id ?>  }}
                            </a>

                            <a href="#reply_message_<?=$i?>" class="card-link"
                               @if(Auth::check())
                                ng-click="postCtrl.replyInReplyDownvote('{{$topic_replies[$i]->id}}',
                                                                          '{{ $uuid }}',
                                                                          '{{$topic_replies[$i]->uuid}}',
                                                                          '{{Auth::user()->uuid}}')"
                               @endif
                               ng-init="postCtrl.replyInReplyDownvoteTally('{{$topic_replies[$i]->id}}')">

                                <i class="fa fa-chevron-down"></i>
                                {{ postCtrl.reply_downvote_<?= $topic_replies[$i]->id ?>  }}
                            </a>

                            <a href="#reply_message_<?=$i?>" class="card-link">Report</a>
                        </p>

                        {{-- Reply in Reply List --}}
                        <div ng-init="postCtrl.replyInReplyList('{{$topic_replies[$i]->id}}')"
                             id="replyInReply_<?= $topic_replies[$i]->id?>" layout-fill>
                            {{ postCtrl.replyInReply_<?= $topic_replies[$i]->id?> }}
                        </div>


                        @if(Auth::user())

                            {{-- Reply in reply form--}}
                            <div id="replyInReply_{{$topic_replies[$i]->id}}"
                                 ng-show="postCtrl.showInReply_{{$topic_replies[$i]->id}}"
                                 ng-init="postCtrl.showInReply_{{$topic_replies[$i]->id}}=true">

                                <div class="media md-margin">
                                    <div class="media-left">
                                        <a href="#">
                                            <img class="media-object"
                                                 width="60px"
                                                 src="{!! Auth::user()->profile_img !!}"
                                                 alt="...">
                                        </a>
                                    </div>
                                    <div class="media-body">
                                        <h4 class="media-heading">
                                            You
                                        </h4>
                                        <div contenteditable="true"
                                             placeholder="Any comments?"
                                             class="panel card"
                                             data-content="test"
                                             id="replyInReplyContainer_{{$topic_replies[$i]->id}}">
                                        </div>
                                        <md-button type="submit"
                                                   ng-click="postCtrl.submitReplyInReply('{{$topic_replies[$i]->id}}',
                                                                                         '{{ $uuid }}',
                                                                                         '{{Auth::user()->uuid}}')"
                                                   class="md-raised md-primary">Submit</md-button>
                                    </div>
                                </div>

                            </div>
                        @endif
                    </div>
                </div>
            </div>
        @endfor
</div>

<script>

    socket.on("reply_append_{{ $uuid }}:App\\Events\\TopicReplyEvent", function(message){

        $.get( "/replyView/", { replyReq: message } )
                .done(function( data ) {
                    $('#reply_append_{{ $uuid }}').prepend(data);
                });
    });

</script>
@endsection