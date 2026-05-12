<?php

$arr_membership_levels = [];

$arr_membership_levels[$int_Free_Member] = [
    'label' => 'Free',
	'path' => $path_create_account,
    'title' => ['体験コース', '體驗課程', 'Trial Course'],
    'fee' => [
        'monthly' => ['無料', '免費', 'Free'],
        'yearly'  => ['無料', '免費', 'Free'],
    ],
    'button_label' => ['登録する', '註冊', 'Sign up'],

    'description' => [
        [
            'category_key' => 'study_style',
            'category_label' => ['学習スタイル', '學習方式', 'Study Style'],
            'items' => [
                ['自習(ROM)', '自學 (ROM / 唯讀)', 'Self-Study (ROM / read-only)'],
            ],
        ],
        [
            'category_key' => 'learning_materials',
            'category_label' => ['教材', '教材', 'Learning materials'],
            'items' => [
                ['JWS Workshop Trial', 'JWS Workshop Trial', 'JWS Workshop Trial'],
                ['---オリジナル教材のお試し版', '---原創教材試用版', '---Trial Version of Original Learning Materials'],
            ],
        ],
        [
            'category_key' => 'grammar_content',
            'category_label' => ['Grammar Content', 'Grammar Content', 'Grammar Content'],
            'items' => [
                ['基本的な説明のみ', '僅提供基本說明', 'Basic Explanation Only'],
                ['---句形', '---句型', '---Sentence Pattern'],
                ['---外国語での対応表現', '---外語對應表達', '---Equivalent Expressions in Other Languages'],
                ['---文法の概略説明', '---文法概略說明', '---Grammar Overview'],
                ['---例文', '---例句', '---Example Sentences'],

            ],
        ],
    ],
];


$arr_membership_levels[$int_Basic_Student] = [
    'label' => 'Basic',
	'path' => $path_membership_apply,
    'title' => ['N5-N4 自習コース', 'N5-N4 自習課程', 'N5-N4 Self-Study Course'],
	'target_level' => ['N5-N4', 'N5-N4', 'N5-N4'],
    'fee' => $arr_membership_prices[$int_Basic_Student],
    'button_label' => ['プラン変更', '變更方案', 'Change plan'],

    'description' => [
        [
            'category_key' => 'study_style',
            'category_label' => ['学習スタイル', '學習方式', 'Study Style'],
            'items' => [
                ['自習(ROM)', '自學 (ROM / 唯讀)', 'Self-Study (ROM / read-only)'],
            ],
        ],
        [
            'category_key' => 'learning_materials',
            'category_label' => ['教材', '教材', 'Learning materials'],
            'items' => [
                ['JWS Workshop', 'JWS Workshop', 'JWS Workshop'],
                ['---オリジナル教材', '---原創教材', '---Original Learning Materials'],
            ],
        ],
        [
			'category_key' => 'grammar_content',
			'category_label' => ['Grammar Content', 'Grammar Content', 'Grammar Content'],
			'items' => [
				['基本的な説明 + 高度な説明', '基本說明＋進階說明', 'Basic + Advanced Explanations'],
				['---会話例', '---會話例', '---Conversation Examples'],
				['---文法の作り方', '---文法的構成方式', '---How the Grammar Is Formed'],
				['---文法の使いかた など', '---文法的用法 等等', '---How to Use the Grammar, etc.'],
			],
		],
        [
            'category_key' => 'quiz',
            'category_label' => ['Quiz', '測驗', 'Quiz'],
            'items' => [
                ['利用可', '可使用', 'Available'],
            ],
        ],
        [
            'category_key' => 'bookmark',
            'category_label' => ['Bookmark', '書籤', 'Bookmark'],
            'items' => [
                ['利用可', '可使用', 'Available'],
            ],
        ],
    ],
];

$arr_membership_levels[$int_Plus_Student] = [
    'label' => 'Plus',
	'path' => $path_membership_apply,
    'title' => ['N5-N4 サポートコース', 'N5-N4 支援課程', 'N5-N4 Self-Study + Support Course'],
	'target_level' => ['N5-N4', 'N5-N4', 'N5-N4'],
    'fee' => $arr_membership_prices[$int_Plus_Student],
    'button_label' => ['プラン変更', '變更方案', 'Change plan'],

    'description' => [
        [
            'category_key' => 'study_style',
            'category_label' => ['学習スタイル', '學習方式', 'Study Style'],
            'items' => [
                ['自習 + サポート', '自學 + 支援', 'Self-Study + Support'],
            ],
        ],
        [
            'category_key' => 'learning_materials',
            'category_label' => ['教材', '教材', 'Learning materials'],
            'items' => [
                ['JWS Workshop', 'JWS Workshop', 'JWS Workshop'],
                ['---オリジナル教材', '---原創教材', '---Original Learning Materials'],
            ],
        ],
		[
			'category_key' => 'included_features',
			'category_label' => ['含まれる機能', '包含功能', 'Included Features'],
			'items' => [
				['Basicプランのすべての機能', '包含 Basic 方案的所有功能', 'All features included in the Basic plan'],
			],
		],
		[
			'category_key' => 'additional_support',
			'category_label' => ['追加サポート', '追加支援', 'Additional Support'],
			'items' => [
				['テキストベースによる質問可', '可進行文字形式的提問', 'Text-based questions available'],
				['---週1-2回程度の返信', '---每週約 1-2 次回覆', '---Replies about 1 to 2 times per week'],

			],
		],
    ],
];


$arr_membership_levels[$int_Premium_Student] = [
    'label' => 'Premium',
	'path' => $path_membership_apply,
    'title' => [
		'N5-N1 パーソナルレッスンコース',
		'N5-N1 個人化課程',
		'N5-N1 Personal Lesson Course',
	],
    'target_level' => ['N5-N1', 'N5-N1', 'N5-N1'],
    'fee' => $arr_membership_prices[$int_Premium_Student],
    'button_label' => ['プラン変更', '變更方案', 'Change plan'],

    'description' => [
        [
            'category_key' => 'study_style',
            'category_label' => ['学習スタイル', '學習方式', 'Study Style'],
            'items' => [
                ['自習 + サポート + 授業', '自學 + 支援 + 課程', 'Self-Study + Support + Lessons'],
            ],
        ],
        [
            'category_key' => 'learning_materials',
            'category_label' => ['教材', '教材', 'Learning materials'],
            'items' => [
                ['教材は自由に選択可能', '教材可自由選擇', 'Materials are freely selectable'],
                ['---学生の目的・レベルに合わせて調整', '---依學生目標與程度調整', '---Adjusted to student goals and level'],
            ],
        ],
        [
            'category_key' => 'included_features',
            'category_label' => ['含まれる機能', '包含功能', 'Included Features'],
            'items' => [
                ['Plusプランのすべての機能', '包含 Plus 方案的所有功能', 'All features included in the Plus plan'],
            ],
        ],
        [
			'category_key' => 'classroom',
			'category_label' => ['Classroom参加', '參加 Classroom', 'Join Classroom'],
			'items' => [
				[
					'1対1授業 または グループ授業',
					'1 對 1 或團體課',
					'1-on-1 or group lessons',
				],
				[
					'---通常料金：'
						. $arr_membership_prices[$int_Premium_Student]['lesson_fee']['regular'][$int_used_language_jpn]
						. '（別途）',
					'---一般費用：'
						. $arr_membership_prices[$int_Premium_Student]['lesson_fee']['regular'][$int_used_language_cht]
						. '（另計）',
					'---Standard rate: '
						. $arr_membership_prices[$int_Premium_Student]['lesson_fee']['regular'][$int_used_language_eng]
						. ' (additional)',
				],
				[
					'---特典：'
						. $arr_membership_prices[$int_Premium_Student]['lesson_fee']['benefit'][$int_used_language_jpn],
					'---優惠：'
						. $arr_membership_prices[$int_Premium_Student]['lesson_fee']['benefit'][$int_used_language_cht],
					'---Benefit: '
						. $arr_membership_prices[$int_Premium_Student]['lesson_fee']['benefit'][$int_used_language_eng],
				],
			],
		],

        [
            'category_key' => 'classroom_shared_memo_view',
            'category_label' => ['共有メモ閲覧', '共用筆記閱覽', 'Shared Memo View'],
            'items' => [
                ['Classroom共有メモ閲覧', '可閱覽 Classroom 共用筆記', 'View shared classroom memo'],
            ],
        ],
    ],
];
// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー
$arr_membership_levels[$int_Premium_Student] = [
    'label' => 'Premium',
	'path' => $path_membership_apply,
    'title' => [
		'N5-N1 パーソナルレッスンコース',
		'N5-N1 個人化課程',
		'N5-N1 Personal Lesson Course',
	],
    'target_level' => ['N5-N1', 'N5-N1', 'N5-N1'],
    'fee' => $arr_membership_prices[$int_Premium_Student],
    'button_label' => ['プラン変更', '變更方案', 'Change plan'],
	'description' => [
		[
			'category_key' => 'study_style',
			'category_label' => ['学習スタイル', '學習方式', 'Study Style'],
			'items' => [
				['自習 + サポート + 授業', '自學 + 支援 + 課程', 'Self-Study + Support + Lessons'],
			],
		],
		[
			'category_key' => 'learning_materials',
			'category_label' => ['教材', '教材', 'Learning materials'],
			'items' => [
				['教材は自由に選択可能', '教材可自由選擇', 'Materials are freely selectable'],
				['---学生の目的・レベルに合わせて調整', '---依學生目標與程度調整', '---Adjusted to student goals and level'],
			],
		],
		[
			'category_key' => 'grammar_content',
			'category_label' => ['Grammar Content', 'Grammar Content', 'Grammar Content'],
			'items' => [
				['基本的な説明 + 高度な説明', '基本說明＋進階說明', 'Basic + Advanced Explanations'],
				['---会話例', '---會話例', '---Conversation Examples'],
				['---文法の作り方', '---文法的構成方式', '---How the Grammar Is Formed'],
				['---文法の使いかた など', '---文法的用法 等等', '---How to Use the Grammar, etc.'],
			],
		],
		[
			'category_key' => 'quiz',
			'category_label' => ['Quiz', '測驗', 'Quiz'],
			'items' => [
				['利用可', '可使用', 'Available'],
			],
		],
		[
			'category_key' => 'bookmark',
			'category_label' => ['Bookmark', '書籤', 'Bookmark'],
			'items' => [
				['利用可', '可使用', 'Available'],
			],
		],
		[
			'category_key' => 'classroom',
			'category_label' => ['Classroom参加', '參加 Classroom', 'Join Classroom'],
			'items' => [
				[
					'1対1授業 または グループ授業',
					'1 對 1 或團體課',
					'1-on-1 or group lessons',
				],
				[
					'---通常料金：5,000円 / 1時間（別途）',
					'---一般費用：每小時 NT$1,000（另計）',
					'---Standard rate: USD $35 / hour (additional)',
				],
				[
					'---特典：毎月1時間の授業を無料提供（当月のみ有効）',
					'---優惠：每月提供 1 小時免費課程（僅限當月）',
					'---Benefit: 1 free lesson hour per month (valid for the month only)',
				],
			],
		],

		[
			'category_key' => 'classroom_shared_memo_view',
			'category_label' => ['共有メモ閲覧', '共用筆記閱覽', 'Shared Memo View'],
			'items' => [
				['Classroom共有メモ閲覧', '可閱覽 Classroom 共用筆記', 'View shared classroom memo'],
			],
		],
	],
];
// ーーーーーーーーーーーーーーーーーーーーデバッグ 後で削除ーーーーーーーーーーーーーーーーーーーー
