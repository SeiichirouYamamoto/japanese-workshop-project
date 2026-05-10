<?php

/******************************************************
 *  selected_language
 *  
 ******************************************************/
function jws_get_language_index() {

    $arr_language_paths = [
        1 => '/cht/',
        2 => '/eng/',
    ];

    $request_uri = $_SERVER['REQUEST_URI'] ?? '';

    foreach ($arr_language_paths as $lang_index => $lang_path) {
        if (strpos($request_uri, $lang_path) === 0) {
            return $lang_index;
        }
    }

    return 0;
}

/******************************************************
 *  fullscreen
 *  
 ******************************************************/
function do_display_site_contents(){

	global $arr_fullscreen_pages;

	if(is_page($arr_fullscreen_pages)){
		return false;
	}
	else{
		return true;
	}
}

/******************************************************
 *  children_links
 *  
 ******************************************************/
function build_html_children_links_tree() {

	global $arr_tree_visible_pages;

	if (!is_array($arr_tree_visible_pages)) {
        $arr_tree_visible_pages = array();
    }

	return recursive_build_html_children_links_tree(null, $arr_tree_visible_pages);
}
/******************************************************
 *  account_links
 *  
 ******************************************************/
function build_html_account_links_tree() {

	global
		$int_page_id_jpn_account,
		$int_page_id_cht_account,
		$int_page_id_jpn_login,
		$int_page_id_cht_login,
		$int_page_id_jpn_logout,
		$int_page_id_cht_logout,
		$int_page_id_jpn_profile,
		$int_page_id_cht_profile,
		$int_page_id_jpn_create_account,
		$int_page_id_cht_create_account,
		$int_page_id_jpn_delete_account,
		$int_page_id_cht_delete_account,
		$int_page_id_jpn_change_password,
		$int_page_id_cht_change_password,
		$int_page_id_jpn_forgot_password,
		$int_page_id_cht_forgot_password;

    $arr_visible_logged_out = array(
		// LogIN
		$int_page_id_jpn_login,
		$int_page_id_cht_login,
		// 新規アカウント作成
		$int_page_id_jpn_create_account,
		$int_page_id_cht_create_account,
		// forgot-password
		$int_page_id_jpn_forgot_password,
		$int_page_id_cht_forgot_password,
    );

    $arr_visible_logged_in = array(
		// ログアウト
		$int_page_id_jpn_logout,
		$int_page_id_cht_logout,
		// Profile
		$int_page_id_jpn_profile,
		$int_page_id_cht_profile,
		// パスワード変更
		$int_page_id_jpn_change_password,
		$int_page_id_cht_change_password,
		// アカウント削除
		$int_page_id_jpn_delete_account,
		$int_page_id_cht_delete_account,
    );

	

    $arr_tree_visible_pages = is_user_logged_in() ? $arr_visible_logged_in : $arr_visible_logged_out;

    // accountページ直下の子ページだけを出すなら parent_id は accountページID
    // この関数を accountページで呼ぶ前提なら get_the_ID() のままでOK
    return recursive_build_html_children_links_tree(null, $arr_tree_visible_pages);
}
/******************************************************
 *  site_info_links
 *  
 ******************************************************/
function build_html_site_info_links_tree() {

	global
		$int_page_id_jpn_about_site,
		$int_page_id_cht_about_site,
		$int_page_id_jpn_what_japanese_workshop,
		$int_page_id_cht_what_japanese_workshop,
		$int_page_id_jpn_privacy_policy,
		$int_page_id_cht_privacy_policy,
		$int_page_id_jpn_paid_membership,
		$int_page_id_cht_paid_membership,
		$int_page_id_jpn_legal,
		$int_page_id_cht_legal,
		$int_page_id_jpn_asct,
		$int_page_id_cht_asct,
		$int_page_id_jpn_terms,
		$int_page_id_cht_terms,
		$int_page_id_jpn_contact,
		$int_page_id_cht_contact;

    $arr_tree_visible_pages = [
		$int_page_id_jpn_about_site,
		$int_page_id_cht_about_site,
		$int_page_id_jpn_what_japanese_workshop,
		$int_page_id_cht_what_japanese_workshop,
		$int_page_id_jpn_privacy_policy,
		$int_page_id_cht_privacy_policy,
		$int_page_id_jpn_paid_membership,
		$int_page_id_cht_paid_membership,
		$int_page_id_jpn_legal,
		$int_page_id_cht_legal,
		$int_page_id_jpn_asct,
		$int_page_id_cht_asct,
		$int_page_id_jpn_terms,
		$int_page_id_cht_terms,
		$int_page_id_jpn_contact,
		$int_page_id_cht_contact
	];

    return recursive_build_html_children_links_tree(null, $arr_tree_visible_pages);
}

/******************************************************
 *  Items
 *  
 ******************************************************/
function recursive_build_html_children_links_tree($parent_id = null, $arr_tree_visible_pages = array()) {

    if ($parent_id === null) {
        $parent_id = get_the_ID();
    }

    $children = get_pages(array(
        'parent'      => $parent_id,
        'sort_column' => 'menu_order',
        'sort_order'  => 'ASC',
    ));

    if (empty($children)) {
        return '';
    }

    $str_html = '<ul class="childrenLinksTreeUl">';
    $has_visible_children = false;

    foreach ($children as $child) {

        if (!in_array($child->ID, $arr_tree_visible_pages, true)) {
            continue;
        }

        $has_visible_children = true;

        $str_html .= '<li class="childrenLinksTreeLi">';

        $str_html .= '<a href="'
            . esc_url(get_permalink($child->ID))
            . '">'
            . esc_html($child->post_title)
            . '</a>';

        // 将来「孫ページ」も出したくなったら、同じ可視リストで再帰
        $str_html .= recursive_build_html_children_links_tree($child->ID, $arr_tree_visible_pages);

        $str_html .= '</li>';
    }

    $str_html .= '</ul>';

    if (!$has_visible_children) {
        return '';
    }

    return $str_html;
}
