@props(['userId', 'buttonText' => 'Chat with seller'])

<button data-href="{{ url('chatify/' . $userId) }}"
    class="chat-btn chat-btn-popup  d-inline-flex justify-content-center align-items-center p-1">
    <ion-icon name="chatbubbles-outline" class="icon fs-5"></ion-icon>
</button>
