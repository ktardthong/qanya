@extends('layouts.app')

@section('content')

<div class="row">
    <md-content ng-controller="PostCtrl as postCtrl"
                class="md-padding">

            <div class="layoutSingleColumn">
                {{$is_user}}
                    <span>
                        <i class="fa fa-clock-o fa-x"></i>{{ $created_at }}
                    </span>
                    <span class="pull-right" ng-click="postCtrl.editable='true';
                                                       $('#topicContent').css("background","yellow")">edit</span>

                    <h1 class="md-display-1">
                        {!! HTML::decode($title) !!}
                    </h1>
                    <div class="reading img-fluid" id="topicContent" contenteditable="@{{ postCtrl.editable }}">
                        @{{ postCtrl.editable }}
                        {!! nl2br($body) !!}
                    </div>
                    <div>
                        @if($tags)
                            @foreach($tags as $tag)
                                <a href="/tag/{{$tag}}">#{{$tag}}</a>
                            @endforeach
                        @endif
                    </div>


                <div>
                    <a href="#" id="upvote_btn_status_{{ $uuid }}"
                       class="card-link"
                       ng-init="postCtrl.upvoteTally('{{ $uuid }}')"
                       ng-click="postCtrl.upvote('{{ $uuid }}','{!! $topics_uid !!}')">
                        <i class="fa fa-chevron-up"></i>
                        <span id="upv_cnt_{{$uuid}}">
                            {{--{!! $topic->upvote !!}--}}
                            {{ postCtrl.upvote_<?= $uuid?> }}
                        </span>
                    </a>
                    <a href="#" id="dwnvote_btn_status_{{ $uuid }}"
                       class="card-link"
                       ng-init="postCtrl.dwnvoteTally('{{ $uuid }}')"
                       ng-click="postCtrl.dwnvote('{{ $uuid }}','{!! $topics_uid !!}')">
                        <i class="fa fa-chevron-down"></i>
                    <span id="dwn_cnt_{{$uuid}}">
                        {{ postCtrl.dwnvote_<?= $uuid ?> }}
                    </span>
                    </a>

                </div>
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

                <a href="https://twitter.com/share" class="twitter-share-button">Tweet</a>
                <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0],p=/^http:/.test(d.location)?'http':'https';if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src=p+'://platform.twitter.com/widgets.js';fjs.parentNode.insertBefore(js,fjs);}}(document, 'script', 'twitter-wjs');</script>
                    {{-- end share --}}

                <md-divider></md-divider>
            

                {{-- Author section --}}
                <div class="media md-margin">
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
                                    ng-init="postCtrl.isFollow('{!! $topics_uid !!}')"
                                    ng-click="postCtrl.followUser('{!! $topics_uid !!}')">
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

                @{{ postCtrl.replyList }}
                    <div
                         ng-repeat="reply in postCtrl.replyList"
                         >

                        @{{reply }}

                    </div>

                    <md-list id="reply_append_{{$uuid}}"></md-list>

                    @for($i=0;$i<count($topic_replies);$i++)
                     
                    <md-list class="row">

                        <md-list-item class="md-3-line">
                            <img ng-src="{!! $topic_replies[$i]->profile_img !!}"
                                 class="md-avatar"/>
                            <div class="md-list-item-text">
                                <h3>
                                    <a href="/{{ $topic_replies[$i]->displayname }}" target="_blank">
                                    {{ $topic_replies[$i]->firstname }}
                                    </a>

                                    <small> -
                                        <span am-time-ago="'{!! $topic_replies[$i]->replycreated_at !!}' | amParse:'YYYY-MM-DD HH:mm:ss'"></span>
                                    </small>
                                </h3>
                                <p> {!! HTML::decode($topic_replies[$i]->body) !!} </p>

                            </div>
                        </md-list-item>
                    </md-list>

                    @endfor
                
            
            </div>

    <script>

        socket.on("reply_append_{{ $uuid }}:App\\Events\\TopicReplyEvent", function(message){

            console.log(message);
            $.get( "/replyView/", { replyReq: message } )
                    .done(function( data ) {
                        $('#reply_append_{{ $uuid }}').prepend(data);

                    });
        });

    </script>
@endsection