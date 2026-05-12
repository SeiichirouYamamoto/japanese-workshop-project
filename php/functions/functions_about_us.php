<?php

function build_html_legal_asct($int_selected_language){

	global
		$int_used_language_jpn,
		$int_used_language_cht,
		$arr_membership_prices,
		$int_Premium_Student,
		$str_mysite_mail_address_info;

	$arr_header_title = ['特定商取引法に基づく表記','依消費保護法之表示'];
    $arr_last_updated = ['最終更新日：2026年2月16日','最後更新日期：2026年2月16日'];

    $arr_company_name        = ['Japanese Workshop (山本誠一郎)','Japanese Workshop (山本誠一郎)'];
    $arr_company_address     = ['No.87, Yongle St, Changhua City, Changhua County, Taiwan','台灣彰化縣彰化市永樂街87號'];
    $arr_company_contact     = [
		'メールアドレス：' . $str_mysite_mail_address_info
		,
		'電子郵件：' . $str_mysite_mail_address_info];
    $arr_sales_price = [
		// JP
		'月額 ' . $arr_membership_prices[$int_Premium_Student]['monthly'][$int_used_language_jpn] .
		' / 年額 ' . $arr_membership_prices[$int_Premium_Student]['yearly'][$int_used_language_jpn] .
		'（' . $arr_membership_prices[$int_Premium_Student]['lesson_fee']['benefit'][$int_used_language_jpn] . '）'
		,
		// CHT
		'每月 ' . $arr_membership_prices[$int_Premium_Student]['monthly'][$int_used_language_cht] .
		' / 每年 ' . $arr_membership_prices[$int_Premium_Student]['yearly'][$int_used_language_cht] .
		'（' . $arr_membership_prices[$int_Premium_Student]['lesson_fee']['benefit'][$int_used_language_cht] . '）'
	];
    $arr_additional_fees = [
		// JP
		'追加レッスンをご希望の場合、' .
		$arr_membership_prices[$int_Premium_Student]['lesson_fee']['regular'][$int_used_language_jpn] .
		'の授業料が発生します。'
		,
		// CHT
		'如需加購課程，將收取' .
		$arr_membership_prices[$int_Premium_Student]['lesson_fee']['regular'][$int_used_language_cht] .
		'之費用。'
	];
    $arr_payment_methods     = ['銀行振込','銀行轉帳'];
    $arr_payment_timing = [
		'お申込み後、所定期限までにお振込みいただき、入金確認後に提供開始',
		'申請後請於期限內完成轉帳，確認入帳後開始提供'
	];
    $arr_delivery_timing = [
		'サイト利用：入金確認後、直ちに利用可能 / オンライン授業：日程調整のうえ実施（予約制）',
		'網站使用：確認入帳後立即可用 / 線上課程：採預約制，協調時間後提供'
	];
	$arr_cancellation_policy = [
		'サービスの性質上、当期の返金はできません。サブスクリプションの自動更新停止は更新日の前日までにお手続きください。停止は次回以降の利用期間から適用されます。オンライン授業は予約制です。授業のキャンセル・日程変更は事前にご連絡ください（当日キャンセル等の扱いは別途定めます）。',
		'因服務性質之故，當期費用恕不退還。訂閱制方案之自動續約停止，須於下一期扣款日前一日完成取消手續，方自次一期起生效。線上課程採預約制；如需取消或更改時間，請事先通知（當日取消等之處理方式另行規定）。'
	];
    $arr_service_conditions = [
		'オンライン日本語学習サービス（会員サイト利用＋オンライン授業）',
		'線上日語學習服務（會員網站使用＋線上課程）'
	];
    $arr_system_requirements = ['PC または スマートフォン / 最新ブラウザ','電腦或手機 / 最新瀏覽器'];
    $arr_special_terms       = ['特になし','無'];



	// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー
	$arr_company_name        = ['山本誠一郎','山本誠一郎'];
	$arr_sales_price = [
		// JP
		'月額 ' . $arr_membership_prices[$int_Premium_Student]['monthly'][$int_used_language_jpn] .
		'（' . $arr_membership_prices[$int_Premium_Student]['lesson_fee']['benefit'][$int_used_language_jpn] . '）'
		,
		// CHT
		'每月 ' . $arr_membership_prices[$int_Premium_Student]['monthly'][$int_used_language_cht] .
		'（' . $arr_membership_prices[$int_Premium_Student]['lesson_fee']['benefit'][$int_used_language_cht] . '）'
	];
	// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー



    $arr_legal_asct = [

        'sellerName' => [
            // 'title' => ['事業者名（責任者）','事業者名稱（負責人）'],
			'title' => ['責任者名','負責人姓名'],
            'description' => $arr_company_name
        ],

        'sellerAddress' => [
            'title' => ['所在地','所在地'],
            'description' => $arr_company_address
        ],

        'sellerContactInformation' => [
            'title' => ['連絡先','連絡方式'],
            'description' => $arr_company_contact
        ],

        'salesPrice' => [
            'title' => ['販売価格','販售價格'],
            'description' => $arr_sales_price
        ],

        'additionalFees' => [
            'title' => ['追加手数料','附加費用'],
            'description' => $arr_additional_fees
        ],

        'paymentMethods' => [
            'title' => ['支払方法','付款方式'],
            'description' => $arr_payment_methods
        ],

        'paymentTiming' => [
            'title' => ['支払時期','付款時間'],
            'description' => $arr_payment_timing
        ],

        'deliveryTiming' => [
            'title' => ['提供時期','提供時間'],
            'description' => $arr_delivery_timing
        ],

        'cancellationAndRefundPolicy' => [
            'title' => ['キャンセル・返金について','取消與退款政策'],
            'description' => $arr_cancellation_policy
        ],

        'serviceConditions' => [
            'title' => ['サービス提供条件','服務提供條件'],
            'description' => $arr_service_conditions
        ],

        'systemRequirements' => [
            'title' => ['動作環境','系統需求'],
            'description' => $arr_system_requirements
        ],

        'specialTermsAndConditions' => [
            'title' => ['特記事項','特別條款'],
            'description' => $arr_special_terms
        ]

    ];

    $str_html  = '<h3 class="legalAsctHeader">' . $arr_header_title[$int_selected_language] . '</h3>';
    $str_html .= '<table class="legalAsctTable">';

    foreach ($arr_legal_asct as $row) {
        $str_html .= '<tr>';
        $str_html .= '<th>' . $row['title'][$int_selected_language] . '</th>';
        $str_html .= '<td>' . $row['description'][$int_selected_language] . '</td>';
        $str_html .= '</tr>';
    }

    $str_html .= '</table>';
    $str_html .= '<div class="legalAsctUpdated">' . $arr_last_updated[$int_selected_language] . '</div>';

    return $str_html;
}

function build_html_what_japanese_workshop($int_selected_language){

	global $path_images_what_japanese_workshop;

	$str_img_base_url = get_home_url(
		get_main_site_id(),
		trailingslashit(ltrim($path_images_what_japanese_workshop, '/'))
	);

    $arr_page_title = ['Japanese Workshopとは', '關於 Japanese Workshop'];

    $arr_sections = [

        // --------------------
        // HERO / INTRO
        // --------------------
        [
			'key' => 'intro',
			'title' => ['Japanese Workshopとは', 'Japanese Workshop 是什麼'],
			'image' => $str_img_base_url . 'jws.png',
			'paragraphs' => [
				[
					'Japanese Workshopは、',
					'「共に学び、共に育ち、共に未来を創る」という理念のもとに生まれた、',
					'あたらしい日本語学習体験を創造する学びの場です。',
					'私たちは、学習を単なる知識の獲得とは捉えておりません。',
					'学びとは、自らの可能性を広げ、未来を形づくる営みであると考えています。',
					'その実現のために、次の三つの視点を中核に据えています。'
				],
				[
					'Japanese Workshop 是在「一起學習、一起成長、一起創造未來」的理念下誕生的學習場域。',
					'我們致力於創造全新的日語學習體驗。',
					'我們不把學習視為單純的知識獲取；',
					'學習是擴展可能性、形塑未來的行動。',
					'為了實現這一點，我們將以下三個視角作為核心。'
				]
			]
		],


        // --------------------
        // 3 PILLARS
        // --------------------
        [
            'key' => 'pillars',
            'title' => ['三つの中核', '三個核心'],
            'items' => [

                [
                    'key' => 'direction',
                    'title' => ['1. 方向性を作る', '1. 建立方向'],
                    // 'image' => $str_img_base_url . 'pillar_direction.png',
                    'paragraphs' => [
                        [
                            '学習者自身が「どこに向かうのか」を明確にし、目的意識を持って学べる状態を整えます。',
                            '目標を意識することで、学びは受け身の作業から、自ら選び取る行動へと変わります。'
                        ],
                        [
                            '協助學習者釐清「要走向哪裡」，建立帶著目的的學習狀態。',
                            '當目標清楚，學習會從被動作業轉為主動選擇的行動。'
                        ]
                    ]
                ],

                [
                    'key' => 'cycle',
                    'title' => ['2. 学びを回す', '2. 讓學習循環'],
                    // 'image' => $str_img_base_url . 'pillar_cycle.png',
                    'paragraphs' => [
                        [
                            '学んだ知識やスキルを繰り返し使いながら、理解を深め、定着へと導きます。',
                            '一度理解した内容を、別の文脈や別の角度から再び扱うことで、知識は“使える力”へと変化していきます。'
                        ],
                        [
                            '透過反覆使用已學知識與技能，加深理解並促進定著。',
                            '把同一內容放到不同情境與角度再處理，知識就會轉化為「可運用的能力」。'
                        ]
                    ]
                ],

                [
                    'key' => 'apply',
                    'title' => ['3. 学びを活かす', '3. 活用學習'],
                    // 'image' => $str_img_base_url . 'pillar_apply.png',
                    'paragraphs' => [
                        [
                            '学習内容を現実の場面へと結びつけ、実際に使える「生きた学び」へと高めます。',
                            '理解したことを運用できてこそ、学びは本物になります。'
                        ],
                        [
                            '把學習內容連結到真實場景，提升為能實際使用的「活的學習」。',
                            '能把理解運用出來，學習才算真正到位。'
                        ]
                    ]
                ]

            ]
        ],

		// --------------------
		// WHAT W.I.S.E.
		// --------------------
		[
			'key' => 'wise',
			'title' => ['What W.I.S.E.', 'What W.I.S.E.'],
			'image' => $str_img_base_url . 'wise.png',
			'paragraphs' => [
				[
					'私たちは、この理念および学びの核を実現するために、',
					'独自の学習システム W.I.S.E.（Whiteboard Interactive System for Japanese Education）を開発しました。',
					'W.I.S.E.は、方向性を明確にし、学びを循環させ、実際に活かすための設計思想をもとに構築されています。',
					'学習を一方向の情報伝達に終わらせず、',
					'学習者自身が構造を理解し、考え、使い、育てていくための環境です。',
					'このW.I.S.E.を基盤として、私たちは二つの体験を提供しています。'
				],
				[
					'為了實現這一理念與學習核心，我們開發了獨自的學習系統 W.I.S.E.（Whiteboard Interactive System for Japanese Education）。',
					'W.I.S.E. 以明確方向、促進循環並落實活用為設計思想所構築。',
					'它不只是單向的知識傳遞，而是一個讓學習者理解結構、思考、運用並持續成長的環境。',
					'以 W.I.S.E. 為基礎，我們提供兩種學習體驗。'
				]
			]
		],


        // --------------------
        // TWO EXPERIENCES
        // --------------------
        [
            'key' => 'experiences',
            'title' => ['二つの体験', '兩種體驗'],
            'items' => [

                [
                    'key' => 'lesson_experience',
                    'title' => ['1. 授業体験 ― 設計された学びの空間', '1. 課堂體驗——被設計的學習空間'],
                    // 'image' => $str_img_base_url . 'experience_lesson.png',
                    'paragraphs' => [
                        [
                            '授業は、単なる説明の場ではありません。',
                            'それは、「どうなりたいのか」「何をすればよいのか」「これまで何を積み重ねてきたのか」を見つめ直し、学びの意味を再確認する時間です。',
                            '理解だけでなく、納得と実感を伴う“方向づけの時間”。学習の軸を整える場です。'
                        ],
                        [
                            '課堂不只是說明的場所。',
                            '它是重新凝視「想成為什麼」「該做什麼」「至今累積了什麼」，並再次確認學習意義的時間。',
                            '不只理解，更包含認同與實感的「定向時間」，用來整理學習主軸。'
                        ]
                    ],
                    'features' => [

                        [
                            'key' => 'lesson_contents',
                            'name' => ['Lesson Contents', 'Lesson Contents'],
                            'image' => $str_img_base_url . 'lessonContents.png',
                            'paragraphs' => [
                                [
                                    '授業は場当たり的に進みません。設計された内容に基づき、学びは段階的に積み上がっていきます。',
                                    '積み重ねが見えることで、成長を実感できます。'
                                ],
                                [
                                    '課堂不是臨時起意地推進；依照設計內容逐步累積。',
                                    '看得見累積，就能更清楚感受成長。'
                                ]
                            ]
                        ],

                        [
                            'key' => 'grammar_explanation',
                            'name' => ['Grammar Explanation', 'Grammar Explanation'],
                            'image' => $str_img_base_url . 'grammarExplanation.png',
                            'paragraphs' => [
                                [
                                    '文法を単なる暗記対象として扱いません。構造を理解し、「なぜそうなるのか」を明らかにします。',
                                    '表面的な知識ではなく、仕組みを理解することで応用力が育ちます。'
                                ],
                                [
                                    '不把文法當作死背；重視結構理解，釐清「為什麼會這樣」。',
                                    '理解機制而非表層知識，才能培養應用力。'
                                ]
                            ]
                        ],

                        [
                            'key' => 'grammar_insights',
                            'name' => ['Grammar Insights', 'Grammar Insights'],
                            'image' => $str_img_base_url . 'grammarInsights.png',
                            'paragraphs' => [
                                [
                                    '関連する文法を横断的に整理し、比較・共通点・違いを明確にします。',
                                    '似ている表現を関係性の中で捉えることで、使い分けの感覚を養い、誤解を減らします。',
                                    '文法を点ではなく、構造として理解するための機能です。'
                                ],
                                [
                                    '把相關文法橫向整理，明確比較、共通點與差異。',
                                    '在關係中理解相似表現，可培養區別使用的感覺並減少誤解。',
                                    '這是讓文法以「結構」而非「點」來理解的功能。'
                                ]
                            ]
                        ]

                    ]
                ],

                [
                    'key' => 'outside_experience',
                    'title' => ['2. 授業以外の体験 ― 学びを回し、活かす空間', '2. 課外體驗——讓學習循環並活用的空間'],
                    // 'image' => $str_img_base_url . 'experience_outside.png',
                    'paragraphs' => [
                        [
                            '学びは、授業の中だけで完結するものではありません。',
                            '理解を深め、定着させ、実際に使える力へと変えるためには、授業外での循環が不可欠です。',
                            'Japanese Workshopは、その循環を支える環境を整えています。'
                        ],
                        [
                            '學習不會只在課堂內完成。',
                            '要加深理解、促進定著並轉為可用能力，課外循環不可或缺。',
                            'Japanese Workshop 提供支援循環的環境。'
                        ]
                    ],
                    'features' => [

                        [
                            'key' => 'workshop',
                            'name' => ['Workshop', 'Workshop'],
                            'image' => $str_img_base_url . 'workshop.png',
                            'paragraphs' => [
                                [
                                    '授業で扱われた文法内容を、学習者自身がいつでも閲覧できる場所です。',
                                    '予習として事前に目を通すことで、授業での理解がより深まります。',
                                    '復習として振り返ることで、学びを確かなものにします。'
                                ],
                                [
                                    '學習者可隨時回看課堂中使用的文法內容。',
                                    '作為預習能提升課堂理解；作為復習能讓學習更穩固。'
                                ]
                            ]
                        ],

                        [
                            'key' => 'quiz',
                            'name' => ['Quiz', 'Quiz'],
                            'image' => $str_img_base_url . 'quiz.png',
                            'paragraphs' => [
                                [
                                    '理解を確認し、定着へと導きます。',
                                    '単なる反復ではなく、構造理解に基づく確認を行います。',
                                    '弱点が明確になり、次の学びへとつながります。'
                                ],
                                [
                                    '用來確認理解並引導定著。',
                                    '不是單純重複，而是基於結構理解的檢核；弱點更清楚，能連到下一步學習。'
                                ]
                            ]
                        ],

                        [
                            'key' => 'bookmark',
                            'name' => ['Bookmark', 'Bookmark'],
                            'image' => $str_img_base_url . 'bookmark.png',
                            'paragraphs' => [
                                [
                                    '気づきや重要な内容を、自分の資産として保存できます。',
                                    '学びは蓄積され、いつでも振り返ることができます。'
                                ],
                                [
                                    '把重要內容與發現保存成自己的資產，隨時可回顧。'
                                ]
                            ]
                        ],

                        [
                            'key' => 'lesson_memo',
                            'name' => ['Lesson Memo', 'Lesson Memo'],
                            'image' => $str_img_base_url . 'lessonMemo.png',
                            'paragraphs' => [
                                [
                                    '授業内容がここで共有され、いつでも確認できる場所です。',
                                    '教師が授業中に記録したメモが共有され、扱ったポイントや重要事項を後から見直すことができます。',
                                    '授業の内容を形として残すことで、学びを一過性のものにせず、継続的な理解へとつなげます。'
                                ],
                                [
                                    '課堂內容在此共享並可隨時查看。',
                                    '教師課中記錄的重點可回顧，使學習不流於一次性而能連到持續理解。'
                                ]
                            ]
                        ]

                    ]
                ]

            ]
        ],

        // --------------------
        // VISION
        // --------------------
        [
            'key' => 'vision',
            'title' => ['Japanese Workshopが目指すもの', 'Japanese Workshop 的目標'],
            // 'image' => $str_img_base_url . 'vision.png',
            'paragraphs' => [
                [
                    '授業で方向をつくる。授業外で学びを回す。そして、それを自分の力に変える。',
                    'Japanese Workshopは、「意味ある学びの構築・継続・活用」を支える学習環境です。',
                    'ここでは、学習者は受け身ではありません。自ら考え、選び、使い、育てていく存在です。',
                    '学びを“受け取る人”ではなく、学びを“創り出す人”になること。',
                    'それが、Japanese Workshopの目指す姿です。'
                ],
                [
                    '在課堂建立方向，在課外讓學習循環，並把它轉為自己的力量。',
                    'Japanese Workshop 支援「有意義的學習之建構、持續與活用」。',
                    '學習者不是被動接受者，而是能思考、選擇、使用並成長的行動者。',
                    '從「接收學習的人」成為「創造學習的人」，這就是 Japanese Workshop 的目標。'
                ]
            ]
        ]

    ];

    $str_html  = '<div class="whatJwWrap">';
    $str_html .= '<h1 class="aboutUsH1">' . $arr_page_title[$int_selected_language] . '</h1>';

    foreach($arr_sections as $section){
        $str_html .= build_html_what_jw_section($section, $int_selected_language);
    }

    $str_html .= '</div>';

    return $str_html;
}

function build_html_what_jw_section($section, $int_selected_language){

    $str_title = $section['title'][$int_selected_language];

    $str_html  = '<section class="aboutUsSection">';
    $str_html .= '<h2 class="aboutUsH2">' . $str_title . '</h2>';

    if(isset($section['image']) && $section['image'] !== ''){
        $str_html .= '<div class="aboutUsImageWrap">';
        $str_html .= '<img class="aboutUsImage" src="' . jws_add_file_version($section['image']) . '" alt="">';
        $str_html .= '</div>';
    }

    if(isset($section['paragraphs']) && isset($section['paragraphs'][$int_selected_language])){
        foreach($section['paragraphs'][$int_selected_language] as $p){
            $str_html .= '<p class="whatJwP">' . $p . '</p>';
        }
    }

    if(isset($section['items']) && is_array($section['items']) && !empty($section['items'])){
        $str_html .= '<div class="whatJwCards">';
        foreach($section['items'] as $item){
            $str_html .= build_html_what_jw_card($item, $int_selected_language);
        }
        $str_html .= '</div>';
    }

    $str_html .= '</section>';

    return $str_html;
}

function build_html_what_jw_card($item, $int_selected_language){

    $str_html  = '<div class="whatJwCard">';

    if(isset($item['image']) && $item['image'] !== ''){
        $str_html .= '<div class="whatJwCardImageWrap">';
        $str_html .= '<img class="whatJwCardImage" src="' . jws_add_file_version($item['image']) . '" alt="">';
        $str_html .= '</div>';
    }

    $str_html .= '<h3 class="whatJwH3">' . $item['title'][$int_selected_language] . '</h3>';

    if(isset($item['paragraphs']) && isset($item['paragraphs'][$int_selected_language])){
        $str_html .= build_html_paragraphs($item['paragraphs'][$int_selected_language]);
    }

    if(isset($item['features']) && is_array($item['features']) && !empty($item['features'])){
        $str_html .= '<div class="whatJwFeatures">';
        foreach($item['features'] as $feature){
            $str_html .= build_html_what_jw_feature($feature, $int_selected_language);
        }
        $str_html .= '</div>';
    }

    $str_html .= '</div>';

    return $str_html;
}

function build_html_what_jw_feature($feature, $int_selected_language){

    $has_image = (isset($feature['image']) && $feature['image'] !== '');
    $str_class = 'whatJwFeature';
    if(!$has_image){
        $str_class .= ' noImage';
    }

    $str_html  = '<div class="' . $str_class . '">';

    if($has_image){
        $str_html .= '<div class="whatJwFeatureImageWrap">';
        $str_html .= '<img class="whatJwFeatureImage" src="' . jws_add_file_version($feature['image']) . '" alt="">';
        $str_html .= '</div>';
    }

    $str_html .= '<div class="whatJwFeatureBody">';
    $str_html .= '<div class="whatJwFeatureName">' . $feature['name'][$int_selected_language] . '</div>';

    if(isset($feature['paragraphs']) && isset($feature['paragraphs'][$int_selected_language])){
        $str_html .= build_html_paragraphs($feature['paragraphs'][$int_selected_language]);
    }

    $str_html .= '</div>';
    $str_html .= '</div>';

    return $str_html;
}

function build_html_paragraphs($arr_paragraphs){

    $str_html = '';
    foreach($arr_paragraphs as $p){
        $str_html .= '<p class="whatJwP">' . $p . '</p>';
    }
    return $str_html;
}
