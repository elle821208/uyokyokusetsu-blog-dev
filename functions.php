<?php
//※※※↑↑↑functions.phpトップの<?phpより上にはコメントを書かないこと(エラーの原因になる)！※※※

// ------------------------------------------
// サムネイル画像（アイキャッチ）を使う設定
// ------------------------------------------
// 投稿や固定ページでアイキャッチ画像（サムネイル）を使えるようにします。
add_theme_support('post-thumbnails'); 
add_image_size('post-thumbnails', 400, 200, true); // 幅400×高さ200（トリミングあり）
add_image_size('custom-thumb', 640, 360, true);    // 幅640×高さ360（トリミングあり）

// ------------------------------------------
// タブのタイトルに表示する文字列をカスタマイズ
// ------------------------------------------
// 例）「mindset | サイト名」などに表示されます。
// 対象ページ: トップページ、カテゴリページ、記事ページなど
// 影響ファイル: header.php などタイトルを出力しているファイル
function titles() {
    $title = wp_title(' | ', true, 'right');
    if (is_home()) {
        echo '①紆余曲折 |トップ ';
    } elseif (is_category()) {
        single_cat_title();
        echo ' | サイト名';
    } else {
        echo $title . 'サイト名';
    }
}

// ------------------------------------------
// 通常投稿（post）のアーカイブURLを /blog に変更
// ------------------------------------------
// URL例: https://〇〇.com/blog でアーカイブ表示されます
function post_has_archive($args, $post_type) {
    if ('post' === $post_type) {
        $args['rewrite'] = true;
        $args['has_archive'] = 'blog';
        $args['label'] = '雑記ブログ一覧';
    }
    return $args;
}
add_filter('register_post_type_args', 'post_has_archive', 10, 2);

// ------------------------------------------
// トップページ（front-page.php）の投稿表示数を12件に設定
function news_posts_per_page($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if ($query->is_front_page()) {
        $query->set('posts_per_page', 12);
    }
}
add_action('pre_get_posts', 'news_posts_per_page');







// ------------------------------------------
// カスタム投稿タイプ「works」（技術ブログ）をつくる
// ------------------------------------------
// ★これを使うと、WordPress に “投稿・固定ページ” 以外の
//   新しい記事の種類を追加できる。
// ★ここでは「技術記事だけを入れる箱（works）」を作る。

// 追加説明①：cpy_register_works の細かい意味
// ● cpy → あなたのテーマの頭文字（prefix）
//          「他の人の関数名とぶつけないための名前タグ」
// ● register → WordPress に「登録してね」とお願いする意味
// ● works → 新しく作る投稿タイプのID（技術ブログ専用の箱）
// → つまり関数名全体の意味は『cpyテーマで "works" を登録する先生』


// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★
// ▼▼▼▼▼▼▼▼▼▼▼▼▼ 使用者（あなた）視点での動く順番 ▼▼▼▼▼▼▼▼▼▼▼▼▼
// ① WordPress が起動する
// ② init というタイミングが来る
// ③ add_action が「cpy_register_works 呼んで！」と WordPress に伝える
// ④ cpy_register_works 関数が呼ばれる
// ⑤ $labels（表示名）を設定する
// ⑥ $args（動きのルール）を設定する
// ⑦ register_post_type() で "works" を WP に登録する
// ⑧ WP ダッシュボード左側に「技術ブログ一覧」というメニューが追加される
// ⑨ /works で一覧ページが使えるようになる
// ⑩ 投稿とは別の “技術ブログ専用の箱” が完成
// ▲▲▲▲▲▲▲▲▲▲▲▲▲ 使用者視点の流れを完全理解 ▲▲▲▲▲▲▲▲▲▲▲▲▲
// ★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★★


function cpy_register_works() {

    // ▼投稿タイプの名前（ラベル）をまとめたもの
    //   ※ブログの管理画面に表示される文字。
    $labels = [
        'singular_name' => 'tech',   // 1記事の名前 → 技術記事
        'edit_name'     => 'tech',   // 編集画面の名前
    ];

    // ▼ここでテスト：ラベル名の変更による変化を学習できる
    // ▼変更してダッシュボードでどう変わるか試せるテスト案
    //   'singular_name' => 'recipe',（レシピ）
    //   'singular_name' => 'work',（作品）
    //   'singular_name' => 'memo',（メモ）
    //   管理画面の「投稿名」の表示が変わります。


    // ▼投稿タイプの設定（とても大事な部分）
    $args = [

        // 管理画面のメニューに表示されるタイトル
        'label'               => '技術ブログ一覧',

        // ▼②テスト：menu名を変えて、ダッシュボードがどう変わるか試せる
        // 例：
        // 'label' => 'レシピ倉庫',
        // 'label' => '作品ひきだし',
        // 'label' => 'なんでもノート',
        // → ダッシュボード左メニューの文字が変わる！


        // 管理画面の細かい名前の設定
        'labels'              => $labels,

        // ↓ここからが「wordpressでどんな動きをするか」の設定

        // ① public：公開する？（true → 公開。一般の人も見られる）
        'public'              => true,

        // ② show_in_rest：ブロックエディタを使う？（true → 使える）
        'show_in_rest'        => true,

        // ③ has_archive：一覧ページを作る？
        //     true にすると /works で一覧ページが作られる
        'has_archive'         => true,

        // ④ hierarchical：階層構造？（true→親子関係を作れる）
        //    技術ブログは普通の投稿と同じなので false（親子不要）
        'hierarchical'        => false,

        // ⑤ rewrite：URLのルール
        //    'slug' => 'works'
        //    → URL が /works/◯◯◯ になるように指定
        'rewrite'             => [
            'slug' => 'works',       // スラッグ名（URLの文字）
            'with_front' => true     // /works の前に /blog を付けるか？ → true
        ],

        // ▼②テスト：URLの文字も変えて、実際のURLがどう変わるか学習できる
        // 'slug' => 'techblog',
        // 'slug' => 'portfolio',
        // → /techblog や /portfolio に変わる

        // ⑥ menu_position：管理画面での表示位置
        //    5は “投稿” のすぐ下あたり
        'menu_position'       => 5,

        // ⑦ can_export：エクスポート可能？ → true（OK）
        'can_export'          => true,

        // ⑧ supports：記事編集で使える機能の種類
        //    title → タイトル
        //    editor → 本文
        //    thumbnail → アイキャッチ
        //    page-attributes → 並び替え用（menu_order）
        'supports' => [
            'title',
            'editor',
            'thumbnail',
            'page-attributes'
        ],
    ];

    // ▼WordPress に「新しい投稿タイプ works を作ってね」と登録する
    register_post_type('works', $args);
}

// ▼WordPress が起動したとき（initタイミング）に
//   上のカスタム投稿タイプを読み込むようにする
add_action('init', 'cpy_register_works');











// ------------------------------------------
// 技術ブログ（works）投稿タイプのアーカイブページで
// ?w_year=2025&w_month=07 のような年月絞り込みを可能にする
// ------------------------------------------
// 対象ページ: /works などの works 投稿タイプアーカイブ
// 対象ファイル: archive-works.php（テンプレートファイル）
// 絞り込み条件がないとき → 全 works 表示
// 絞り込みがあるとき → 年月一致する works のみ表示
function filter_works_archive_by_date($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_post_type_archive('works')) {
        // クエリパラメータから「年」と「月」を取得（URL例: /works?w_year=2025&w_month=07）
        $year  = isset($_GET['w_year'])  ? intval($_GET['w_year'])  : null;
        $month = isset($_GET['w_month']) ? intval($_GET['w_month']) : null;

        // 年または月が指定されている場合にのみ、date_query で絞り込む
        if ($year || $month) {
            $date_query = [];
            if ($year)  $date_query['year']  = $year;
            if ($month) $date_query['month'] = $month;
            $query->set('date_query', [$date_query]); // 年月で絞り込み
        }

        // 投稿タイプは「works」のみに限定
        $query->set('post_type', 'works');

        // 表示件数は制限なし（全件表示）
        // 必要に応じて 12 件などに変更可能
        $query->set('posts_per_page', -1);
    }
}
add_action('pre_get_posts', 'filter_works_archive_by_date');

// ------------------------------------------
// トップページでは「zakki」カテゴリのみ表示するように制限
// 対象ページ: front-page.php（トップ）
// 対象投稿タイプ: post（通常投稿）
function filter_main_query_for_front($query) {
    if (is_admin() || !$query->is_main_query()) return;
    if (is_front_page()) {
        $query->set('category_name', 'zakki'); // zakkiカテゴリのみ
    }
}
add_action('pre_get_posts', 'filter_main_query_for_front');

// ------------------------------------------
// 月別アーカイブページで ?cat=◯◯ のカテゴリ絞り込みを許可
// 例: /2025/07/?cat=mindset のような形式
function filter_monthly_archive_by_category($query) {
    if (!is_admin() && $query->is_main_query() && is_date() && isset($_GET['cat'])) {
        $query->set('category_name', sanitize_text_field($_GET['cat']));
    }
}
add_action('pre_get_posts', 'filter_monthly_archive_by_category');

// ------------------------------------------
// カテゴリーページで ?year=2025&monthnum=07 による年月絞り込みを許可
// 対象ページ: /category/mindset など
function filter_archive_by_category_and_date($query) {
    if (!is_admin() && $query->is_main_query() && $query->is_category()) {
        if (isset($_GET['year']))     $query->set('year', intval($_GET['year']));
        if (isset($_GET['monthnum'])) $query->set('monthnum', intval($_GET['monthnum']));
    }
}
add_action('pre_get_posts', 'filter_archive_by_category_and_date');




// =============================================
// Prism.js を読み込むための設定（functions.php）
// =============================================

function add_prismjs_to_theme() {
  // Prism.js の CSS（見た目のスタイル）を読み込む
  wp_enqueue_style(
    'prismjs-css', // スタイルの名前（自由に変更可）
    'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/themes/prism.min.css' // CDNのURL（外部の倉庫）
  );

  // Prism.js の JavaScript（コードを色付けする仕組み）を読み込む
  wp_enqueue_script(
    'prismjs-js', // スクリプトの名前（自由に変更可）
    'https://cdn.jsdelivr.net/npm/prismjs@1.29.0/prism.min.js', // CDNのURL
    array(),  // 依存するスクリプト（なし）
    null,     // バージョン番号（自動）
    true      // 読み込み位置：trueはHTMLの一番下（速くなる）
  );
}

// WordPress に「この関数を使ってね！」と登録する
add_action('wp_enqueue_scripts', 'add_prismjs_to_theme');






// ==============================
// コードコピー機能のJS/CSSを読み込み
// ==============================
function uyokyokusetsu_enqueue_copy_code_assets() {
    // JSを読み込み（テーマの/js/copy-code.js）
    wp_enqueue_script(
        'copy-code',
        get_template_directory_uri() . '/js/copy-code.js',
        array(),
        null,
        true // フッターで読み込む
    );

    // CSSを読み込み（テーマの/css/copy-code.css）
    wp_enqueue_style(
        'copy-code-style',
        get_template_directory_uri() . '/css/copy-code.css'
    );
}
add_action('wp_enqueue_scripts', 'uyokyokusetsu_enqueue_copy_code_assets');





// ==============================
// resposive.css スマホ対応（レスポンシブデザイン）専用の CSS
// ==============================
function theme_responsive_css() {
    wp_enqueue_style(
        'responsive',
        get_template_directory_uri() . '/css/responsive.css',
        array(),
        '1.0.0'
    );
}
add_action('wp_enqueue_scripts', 'theme_responsive_css');



// ==============================
//includes(functions.phpの記載を分担させるための、php機能ファイルの入ったフォルダ)を読み込む
// ==============================
require_once get_template_directory() . '/includes/enqueue.php';
require_once get_template_directory() . '/includes/theme-setup.php';//includesフォルダのtheme-setup.php を読み込む












/* ======================================================
   ▼ 1. 環境判別（本番 / ローカル）
====================================================== */
if ( !defined('WP_ENV') ) {
    if (
        strpos($_SERVER['HTTP_HOST'], 'localhost') !== false ||
        strpos($_SERVER['HTTP_HOST'], '.local') !== false
    ) {
        define('WP_ENV', 'local');   // LocalWP
    } else {
        define('WP_ENV', 'production'); // 本番
    }
}


/* ======================================================
   ▼ 2. ダッシュボード背景色（環境ごと）
====================================================== */
function tetsu_admin_env_style() {

    if (WP_ENV === 'local') {
        echo '<style>
            body.wp-admin { background: #e3f0ff !important; }
        </style>';
    } else {
        echo '<style>
            body.wp-admin { background: #ffe5e5 !important; }
        </style>';
    }
}
add_action('admin_head', 'tetsu_admin_env_style');


/* ======================================================
   ▼ 3. ダッシュボード警告バナー
====================================================== */
add_action('admin_notices', function() {

    if (WP_ENV === 'production') {
        echo '<div style="padding:12px; background:#ff4444; color:#fff; font-size:18px; font-weight:bold; text-align:center;">
        🔴【本番環境】です。操作に注意！
        </div>';
    }

    if (WP_ENV === 'local') {
        echo '<div style="padding:12px; background:#2277ff; color:#fff; font-size:18px; font-weight:bold; text-align:center;">
        🔵【ローカル環境】です。安心して編集できます。
        </div>';
    }

});


/* ======================================================
   ▼ 4. ローカル環境だけ WEBサイトに警告バナー（ヘッダー固定）
====================================================== */
function tetsu_local_front_notice() {
    if (WP_ENV === 'local') {

        // ヘッダー固定バナー
        echo '
        <div style="
            width:100%;
            background:#1133aa;
            color:white;
            padding:12px;
            text-align:center;
            font-size:18px;
            position:fixed;
            top:0;
            left:0;
            z-index:9999;
        ">
            🔵【ローカル環境のサイト】（本番ではありません）
        </div>';

        // バナーの高さ分だけ余白
        echo '<style>
            body { margin-top:50px !important; }
        </style>';
    }
}
add_action('wp_head', 'tetsu_local_front_notice');


/* ======================================================
   ▼ 4-2. LocalWP だけ WEBサイトのフッターにも警告バナー追加
====================================================== */
add_action('wp_footer', function() {
    if (WP_ENV === 'local') {
        echo '
        <div style="
            width:100%;
            background:#1133aa;
            color:white;
            padding:12px;
            text-align:center;
            font-size:16px;
            font-weight:bold;
            margin-top:20px;
        ">
            🔵【ローカル環境】これは開発用サイトです
        </div>';
    }
});


/* ======================================================
   ▼ 4-3. LocalWP のサイト背景色を変更
====================================================== */
add_action('wp_head', function() {
    if (WP_ENV === 'local') {
        echo '<style>
            body { background:#fffbe6 !important; }
        </style>';
    }
});


/* ======================================================
   ▼ 5. 投稿一覧に「完成・途中・放置」のカラータグ
====================================================== */
function tetsu_custom_post_state_tags($states, $post) {

    $status = get_post_status($post->ID);

    // 一度クリア
    $states = array();

    $labels = array(
        'complete' => '<span style="color:#28a745; font-weight:bold;">🟩 完成（公開可能）</span>',
        'progress' => '<span style="color:#f0ad4e; font-weight:bold;">🟨 途中（書きかけ）</span>',
        'paused'   => '<span style="color:#d9534f; font-weight:bold;">🟥 放置（優先度低）</span>',
    );

    switch ($status) {
        case 'publish':
            $states[] = $labels['complete'];
            break;

        case 'draft':
        case 'pending':
            $states[] = $labels['progress'];
            break;

        case 'private':
            $states[] = $labels['paused'];
            break;

        default:
            if (!empty($post->post_password)) {
                $states[] = $labels['paused'];
            }
            break;
    }

    return $states;
}
add_filter('display_post_states', 'tetsu_custom_post_state_tags', 10, 2);



/* ======================================================
   ▼ 6. ダッシュボードに「運用ルール＋作業順」メモ
====================================================== */
function tetsu_add_dashboard_widget() {
    wp_add_dashboard_widget(
        'tetsu_rules_widget',
        '📝 ブログ運用ルール（完成・途中・放置＋作業順）',
        'tetsu_dashboard_rules_display'
    );
}
add_action('wp_dashboard_setup', 'tetsu_add_dashboard_widget');

function tetsu_dashboard_rules_display() {
    echo '
        <div style="font-size:15px; line-height:1.8;">

            <h2 style="margin-bottom:10px;">📌 作業の順番（連番）</h2>
            <ol style="margin-bottom:25px;">
                <li><strong>🟨 途中（書きかけ）記事を進める</strong><br>
                    まずここから。少しでも完成へ。</li>
                <li><strong>🟩 完成（公開可能）を本番へ反映</strong><br>
                    読み直してOKなら公開。</li>
                <li><strong>🟥 放置（優先度低）をチェック</strong><br>
                    やる気がある日に回収。</li>
            </ol>

            <h3>🟩 完成（公開可能）【作業順：2】</h3>
            <ul>
                <li>本番へ反映する候補</li>
                <li>読み直してOKの状態</li>
                <li><strong>WP状態：公開（publish）</strong></li>
            </ul>

            <h3>🟨 途中（書きかけ）【作業順：1】</h3>
            <ul>
                <li>構成がまだ完成していない</li>
                <li>画像・図解が不足</li>
                <li>リライト待ち</li>
                <li><strong>WP状態：下書き（draft）</strong></li>
            </ul>

            <h3>🟥 放置（優先度低）【作業順：3】</h3>
            <ul>
                <li>アイデアだけ</li>
                <li>いつ書くかわからない</li>
                <li>下書きの下書き</li>
                <li><strong>WP状態：非公開（private）</strong></li>
            </ul>

        </div>
    ';
}












// // ==============================
// // 学習用 JavaScript ファイル群
// // ==============================
// function my_enqueue_scripts() {
//     wp_enqueue_script('tetsu-basics',
//         get_template_directory_uri() . '/Tetsu-Js-Study/basics.js',
//         array(), '1.0', true);

//     wp_enqueue_script('tetsu-functions',
//         get_template_directory_uri() . '/Tetsu-Js-Study/functions.js',
//         array(), '1.0', true);

//     wp_enqueue_script('tetsu-arrays-loops',
//         get_template_directory_uri() . '/Tetsu-Js-Study/arraysAndLoops.js',
//         array(), '1.0', true);

//     wp_enqueue_script('tetsu-objects-builtins', 
//         get_template_directory_uri() . '/Tetsu-Js-Study/objectsAndBuiltIns.js',
//         array(), '1.0', true);

//     wp_enqueue_script('tetsu-dom-browser',
//         get_template_directory_uri() . '/Tetsu-Js-Study/domAndBrowser.js',
//         array(), '1.0', true);
// }
// add_action('wp_enqueue_scripts', 'my_enqueue_scripts');



// // ==============================
// // ダークモード＆季節判定 JS を全ページで読み込み
// // ==============================       
// function enqueue_darkmode_season_script() {
//     wp_enqueue_script(
//         'darkmode-season',
//         get_template_directory_uri() . '/Tetsu-Js-Study/darkmode-season.js', // ← フォルダ構成に合わせたパス
//         array(), // 依存スクリプトなし
//         null,    // バージョン番号（キャッシュ防止したいときは time() にすると便利）
//         true     // フッターで読み込む（高速化）
//     );
// }
// add_action('wp_enqueue_scripts', 'enqueue_darkmode_season_script');



    









