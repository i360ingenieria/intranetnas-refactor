@foreach($logs as $log)
    <div class="direct-chat-msg">
        <div class="direct-chat-infos clearfix">
            <span class="direct-chat-name float-left">{{ $log->usuario }}</span>
            <span class="direct-chat-timestamp float-right">{{ $log->created_at  }}</span>
        </div>
        <img class="direct-chat-img" src="/img/user1-128x128.jpg" alt="User Image">
        <div class="direct-chat-text">
            {{ $log->mensaje }}
        </div>

        @foreach($log->respuestas as $resp)
            <div class="direct-chat-msg right">
                <div class="direct-chat-infos clearfix">
                    <span class="direct-chat-name float-right">{{ $resp->usuario }}</span>
                    <span class="direct-chat-timestamp float-left">{{ $resp->created_at  }}</span>
                </div>
                <img class="direct-chat-img" src="/img/user2-128x128.jpg" alt="User Image">
                <div class="direct-chat-text">
                    {{ $resp->respuesta }}
                </div>
            </div>
        @endforeach
    </div>
@endforeach
