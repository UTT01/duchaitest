<head>
    <meta charset="UTF-8">
    <title>Chat Realtime</title>

    <!-- CSS chính -->
    <link rel="stylesheet"
          href="/baitaplon/public/css/GiaoDien_Chat.css">

    <!-- CSS search message -->
    <link rel="stylesheet"
          href="/baitaplon/public/css/SearchMessage_Chat.css">
</head>


<form method="post"
      action="/baitaplon/chat/searchMessage"
      id="searchMessageForm">

       <div class="chat-search-center" id="chatSearch">
        <div class="search-wrapper">
            <input
                type="text"
                id="messageSearchInput"
                name="message_keyword"
                placeholder="Nhập từ cần tìm..."
                autocomplete="off">
        </div>
    </div>



</form>

