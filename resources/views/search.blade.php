<html>

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title>検索サイト</title>
</head>

<body>
    <div class="app">
        <div class="header">
            <div class="left-class">QumuのPDF検索</div>
            <div class="search-class">
                <form id="searchForm" class="search-inputs">
                    <input id="searchQuery" class="search-text" type="text" name="query" />
                    <div class="sukima"></div>
                    <input id="searchButton" type="submit" value="検索" class="button" />
                    <input id="searchStart" type="hidden" name="start" value="0" />
                    <input id="searchNum" type="hidden" name="num" value="20" />
                </form>
            </div>
            <div class="middle-class"></div>
            <div class="upload-class">
                <form method="POST" action="{{ route('upload') }}" enctype="multipart/form-data">
                    @csrf
                    <input type="file" id="file" name="file[]" class="form-control" multiple>
                    <button type="submit" class="button">アップロード</button>
                </form>
            </div>
        </div>

        <div class="main">
            <div class="left-sidebar"></div>
            <div class="content">
                <div class="flex-content">
                    <div id="subheader"></div>
                    <div id="result"></div>
                    <div class="search-contents">
                        <script type="text/javascript" src="http://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
                    </div>
                    <div class="search-contents">
                        <script type="text/javascript" src="fess.js"></script>
                    </div>
                </div>
            </div>
            <div class="right-sidebar"></div>
        </div>
    </div>
</body>

<footer>
    <div class="footer"></div>
</footer>

</html>

<style>
    #searchButton {
        width: 7%;
    }

    .button {
        position: relative;
        background-color: dodgerblue;
        border-radius: 4px;
        color: #fff;
        line-height: 52px;
        -webkit-transition: none;
        transition: none;
    }

    .button:hover {
        background-color: blue;
    }

    .button:active {
        top: 3px;
        box-shadow: none;
    }

    * {
        margin: 0;
        padding: 0;
    }

    .app {
        width: 100vw;
        height: 100vh;
        display: flex;
        flex-direction: column;
    }

    .header {
        display: flex;
        height: 10%;
        width: 100%;
        background-color: lightcyan;
        box-sizing: border-box;
        flex-direction: row;
        align-items: center;
        padding: 10px;
        border-bottom: 2px solid #333;
    }

    .left-class {
        width: 15%;
    }

    .search-class {
        width: 50%;
        background-color: #f2f2f2;
    }

    .middle-class {
        width: 15%;
    }

    .upload-class {
        width: 20%;
    }

    .search-text {
        display: inline-block;
        width: 90%;
        padding: 0.5em;
        border: 1px solid #999;
        box-sizing: border-box;
        background: #f2f2f2;
        margin: 0.5em 0;
    }

    .sukima {
        width: 1%;
    }

    .search-inputs {
        display: flex;
        flex-direction: row;
    }

    .main {
        display: flex;
        flex-direction: row;
        flex-grow: 1;
        height: 90%;
    }

    .left-sidebar {
        width: 20%;
        box-sizing: border-box;
        height: 100%;
    }

    .right-sidebar {
        width: 60%;
        box-sizing: border-box;
        height: 100%;
    }

    .content {
        display: flex;
        flex-direction: column;
        flex-grow: 1;
        align-items: center;
        justify-content: center;
        width: 100%;
        height: 100%;
    }

    .flex-content {
        flex-grow: 1;
        box-sizing: border-box;
        width: 100%;
        height: 100%;
    }

    .search-contents .subheader .result {
        width: 100%;
        height: 100%;
    }
</style>


<script>
    $(function() {
        // (1) Fess の URL
        var baseUrl = "http://devuser.local:8080/json/?q=";
        // (2) 検索ボタンのjQueryオブジェクト
        var $searchButton = $('#searchButton');

        // (3) 検索処理関数
        var doSearch = function(event) {
            // (4) 表示開始位置、表示件数の取得
            var start = parseInt($('#searchStart').val()),
                num = parseInt($('#searchNum').val());
            // 表示開始位置のチェック
            if (start < 0) {
                start = 0;
            }
            // 表示件数のチェック
            if (num < 1 || num > 100) {
                num = 20;
            }
            // (5) 表示ページ情報の取得
            switch (event.data.navi) {
                case -1:
                    // 前のページの場合
                    start -= num;
                    break;
                case 1:
                    // 次のページの場合
                    start += num;
                    break;
                default:
                case 0:
                    start = 0;
                    break;
            }
            // 検索フィールドの値をトリムして格納
            var searchQuery = $.trim($('#searchQuery').val());
            // (6) 検索フォームが空文字チェック
            if (searchQuery.length != 0) {
                var urlBuf = [];
                // (7) 検索ボタンを無効にする
                $searchButton.attr('disabled', true);
                // (8) URL の構築
                urlBuf.push(baseUrl, encodeURIComponent(searchQuery),
                    '&start=', start, '&num=', num);
                // (9) 検索リクエスト送信
                $.ajax({
                    url: urlBuf.join(""),
                    dataType: 'json',
                }).done(function(data) {
                    // 検索結果処理
                    var dataResponse = data.response;
                    // (10) ステータスチェック
                    if (dataResponse.status != 0) {
                        alert("検索中に問題が発生しました。管理者にご相談ください。");
                        return;
                    }

                    var $subheader = $('#subheader'),
                        $result = $('#result'),
                        record_count = dataResponse.record_count,
                        offset = 0,
                        buf = [];
                    if (record_count == 0) { // (11) 検索結果がない場合
                        // サブヘッダー領域に出力
                        $subheader[0].innerHTML = "";
                        // 結果領域に出力
                        buf.push("<b>", dataResponse.q, "</b>に一致する情報は見つかりませんでした。");
                        $result[0].innerHTML = buf.join("");
                    } else { // (12) 検索にヒットした場合
                        var page_number = dataResponse.page_number,
                            startRange = dataResponse.start_record_number,
                            endRange = dataResponse.end_record_number,
                            i = 0,
                            max;
                        offset = startRange - 1;
                        // (13) サブヘッダーに出力
                        buf.push("<br><b>", dataResponse.q, "</b> の検索結果 ",
                            record_count, " 件中 ", startRange, " - ",
                            endRange, " 件目 (", dataResponse.exec_time,
                            " 秒)", "<br><br><br>");
                        $subheader[0].innerHTML = buf.join("");

                        // 検索結果領域のクリア
                        $result.empty();

                        // (14) 検索結果の出力
                        var $resultBody = $("<ol/>");
                        var results = dataResponse.result;
                        for (i = 0, max = results.length; i < max; i++) {
                            buf = [];
                            buf.push('<h3 class="title">', '<a href="',
                                results[i].url_link, '" target="_blank">', results[i].title,
                                '</a></h3><div class="body">', results[i].content_description,
                                '<br/><cite>', '</cite></div><br><br>');
                            $(buf.join("")).appendTo($resultBody);
                        }
                        $resultBody.appendTo($result);

                        // (15) ページ番号情報の出力
                        buf = [];
                        buf.push('<div id="pageInfo">', page_number, 'ページ目<br/>');
                        if (dataResponse.prev_page) {
                            // 前のページへのリンク
                            buf.push('<a id="prevPageLink" href="#">&lt;&lt;前ページへ</a> ');
                        }
                        if (dataResponse.next_page) {
                            // 次のページへのリンク
                            buf.push('<a id="nextPageLink" href="#">次ページへ&gt;&gt;</a><br><br><br>');
                        }
                        buf.push('</div>');
                        $(buf.join("")).appendTo($result);
                    }
                    // (16) ページ情報の更新
                    $('#searchStart').val(offset);
                    $('#searchNum').val(num);
                    // (17) ページ表示を上部に移動
                    $(document).scrollTop(0);
                }).always(function() {
                    // (18) 検索ボタンを有効にする
                    $searchButton.attr('disabled', false);
                });
            }
            // (19) サブミットしないので false を返す
            return false;
        };

        // (20) 検索入力欄でEnterキーが押されたときの処理
        $('#searchForm').submit({
            navi: 0
        }, doSearch);
        // (21) 前ページリンクが押されたときの処理
        $('#result').on("click", "#prevPageLink", {
                navi: -1
            }, doSearch)
            // (22) 次ページリンクが押されたときの処理
            .on("click", "#nextPageLink", {
                navi: 1
            }, doSearch);
    });
</script>
