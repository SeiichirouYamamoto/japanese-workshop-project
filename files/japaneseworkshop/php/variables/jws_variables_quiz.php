<?php


/******************************************************
 *  quizzes
 *  
 ******************************************************/

$int_japanese_particle_quiz_max_count = 3;
$int_arr_distractor_answers_max_count = 5;
$arr_case_particles = [
	[
		'masta_japanese_label_id' => 5728,
		'answer' => 'が',
		'weight' => 5
	],
	[
		'masta_japanese_label_id' => 36437,
		'answer' => 'へ',
		'weight' => 1
	],
	[
		'masta_japanese_label_id' => 44392,
		'answer' => 'を',
		'weight' => 4
	],
	[
		'masta_japanese_label_id' => 27879,
		'answer' => 'と',
		'weight' => 3
	],
	[
		'masta_japanese_label_id' => 26713,
		'answer' => 'で',
		'weight' => 3
	],
	[
		'masta_japanese_label_id' => 30234,
		'answer' => 'に',
		'weight' => 5
	],
	[
		'masta_japanese_label_id' => 7751,
		'answer' => 'から',
		'weight' => 2
	],
	[
		'masta_japanese_label_id' => 38622,
		'answer' => 'まで',
		'weight' => 1
	],
	[
		'masta_japanese_label_id' => 42505,
		'answer' => 'より',
		'weight' => 1
	]
];

$arr_case_particles_reject_pairs = [
	[36437, 30234], //へ に
	[7751, 38622], //から まで
	[7751, 44392], //から を
	[7751, 30234], //から に
	[7751, 42505] //から より
];

$arr_inflection_for_quiz = [
	$int_DictionaryForm,
	$int_NaiForm,
	$int_TaForm,
	$int_NakattaForm,
	$int_TeForm,
	$int_NakuteForm,
	$int_NaideForm,
	$int_BaForm,
	$int_NakerebaForm,
	$int_TaraForm,
	$int_NakattaraForm,
	$int_MasuForm,
	$int_VolitionalForm,
	$int_ImperativeForm,
	$int_ProhibitionForm,
	$int_PotentialVerb,
	$int_PassiveVerb,
	$int_CausativeVerb,
	$int_HonorificVerb,
	$int_CausativePassiveVerb
];



// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_quizMenuBarButtonStart = ['開始','開始'];
$str_quizMenuBarButtonOpenUsersManual = ['使い方','使用方法'];
$str_quizMenuBarButtonReStart = ['やり直し','重新開始'];
$str_quizMenuBarButtonHintPPP = ['ヒント','提示'];
$str_quizMenuBarButtonSort = ['sort','sort'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_quizSettingsScreenTitle = ['設定','設定'];
$str_quizSettingsScreenTitleMasteryLevel = ['Level','Level'];
$str_quizSettingsScreenTitleSubCategory = ['Sub Category','Sub Category'];
$str_quizSettingsScreenTitleJapaneseClassification = ['品詞','詞類'];
$str_quizSettingsScreenTitleWordInflection = ['活用','詞形變化'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_quizHistoryScreenTitle = ['履歴','紀錄'];
$str_quizSuccessScreenTitle = ['活用','詞形變化'];
$str_quizSuccessScreenCorrect = ['正解!','答對了!'];
$str_quizSuccessScreenQuestionTitle = ['問題文','問題'];
$str_quizSuccessScreenExpectedAnswerTitle = ['想定回答','預期答案'];
$str_quizSuccessScreenYourAnswerTitle = ['あなたの回答','您的回答'];
$str_quizSuccessScreenYourAnswerFuriganaTitle = ['ふりがな','注音(平假名)'];
$str_quizFailureScreen = ['残念','好可惜'];
$str_quizFailureScreenMistakeMessage = ['間違いがあるようです。もう一度確認してみてください。','您的答案上好像有一些錯誤。請您確認看看。'];
$str_quizFailureScreenInflectionHint = ['Hint: 語形変化に間違いがあるようです。','Hint: 詞形變化上好像有一些錯誤。'];
$str_quizFailureScreenCorrectAnswerTitle = ['答え','正確答案'];
$str_quizFailureScreenButtonChallengeAgain = ['もう一度','再一次'];
$str_quizButtonFinishQuiz = ['クイズを終了','結束 QUIZ'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_quizButtonShowCorrectAnswer = ['正解を見る','正確答案'];
$str_quizButtonShowNextQuestion = ['次の問題','下一個問題'];
$str_quizButtonShowSkip = ['skip','略過'];
$str_quizButtonShowOtherQuestions = ['他の問題に挑戦','挑戰其他問題'];
$str_quizButtonOpenSettingsScreen = ['設定','設定'];
$str_quizButtonOpenHistoryScreen = ['履歴','紀錄'];
$str_quizButtonHint = ['ヒントを見る','查看提示'];
$str_quizButtonCopy = ['このクイズをコピーする','複製這個Quiz'];
$str_quizCorrectAnswersPrefix = ['答え：','正確答案'];
$str_quizInputPlaceholder = ['解答を入力','輸入答案'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー

// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_quizMessageQuestionInput = ['正解は何ですか？','正確答案是什麼?'];
$str_quizMessageQuestionWhich = ['どちらが正しいですか？','正確答案是哪一個?'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_japaneseParticleQuizTitle = ['助詞クイズ','助詞 QUIZ'];
$str_japaneseParticleQuizMessageQuestion = ['[ ★ ] は同じ助詞です。助詞は何ですか？','下列的句子共通的助詞是什麼?'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_grammarQuizTitle = ['文法クイズ','文法 QUIZ'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_wordInflectionQuizTitle = ['活用クイズ','詞形變化 QUIZ'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_plainformQuizTitle = ['普通形クイズ','普通形 QUIZ'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_sortingQuizTitle = ['並べ替えクイズ','排序 QUIZ'];
$str_sortingQuizMessageQuestion = ['正しい順番に並び替えてください。','請按照正確順序排序。'];
$str_selectQuizContainerGoToQuiz = ['Go To Quiz','Go To Quiz'];
$str_selectQuizContainerQuestion = ['様々なクイズに挑戦してみましょう！','請您挑戰各種各樣的測驗！'];
$str_selectQuizContainerOptionDefault = ['クイズを選んでください。','請選擇Quiz。'];


// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_selectQuizContainerOptionGroupJapaneseParticle = ['「助詞」を勉強したい','我想學習「助詞」'];
$str_selectQuizContainerOptionGroupWordInflection = ['「語形変化」を勉強したい','我想學習「詞形變化」'];
$str_selectQuizContainerOptionGroupPlainform = ['「普通形」を勉強したい','我想學習「普通形」'];
$str_selectQuizContainerOptionGroupGrammar = ['「文法」を勉強したい','我想學習「文法」'];
$str_selectQuizContainerOptionGroupSortingQuiz = ['「並べ替えクイズ」を勉強したい','我想學習「排序Quiz」'];

$str_selectQuizContainerOptionJapaneseParticleQuiz = ['助詞Quiz','助詞Quiz'];
$str_selectQuizContainerOptionJapaneseParticleQuizBeginner = ['入門','入門'];
$str_selectQuizContainerOptionJapaneseParticleQuizBasic = ['基礎','基礎'];
$str_selectQuizContainerOptionJapaneseParticleQuizIntermediate = ['初級','初級'];
$str_selectQuizContainerOptionJapaneseParticleQuizAdvanced = ['中級','中級'];
$str_selectQuizContainerOptionJapaneseParticleQuizExpert = ['上級','上級'];
$str_selectQuizContainerOptionJapaneseParticleQuizMaster = ['高級','高級'];
$str_selectQuizContainerOptionWordInflectionQuiz = ['活用Quiz','詞形變化Quiz'];
$str_selectQuizContainerOptionWordInflectionVerbDictionaryQuiz = ['動詞辞書形','動詞辞書形'];
$str_selectQuizContainerOptionWordInflectionVerbNaiQuiz = ['動詞ない形','動詞ない形'];
$str_selectQuizContainerOptionWordInflectionVerbTeQuiz = ['動詞て形','動詞て形'];
$str_selectQuizContainerOptionWordInflectionVerbTaQuiz = ['動詞た形','動詞た形'];
$str_selectQuizContainerOptionWordInflectionVerbBaQuiz = ['動詞ば形','動詞ば形'];
$str_selectQuizContainerOptionWordInflectionVerbVolitionalQuiz = ['動詞意向形','動詞意向形'];
$str_selectQuizContainerOptionWordInflectionVerbImperativeQuiz = ['動詞命令形','動詞命令形'];
$str_selectQuizContainerOptionWordInflectionPotentialVerbQuiz = ['可能動詞','可能動詞'];
$str_selectQuizContainerOptionWordInflectionPassiveVerbQuiz = ['受身動詞','受身動詞'];
$str_selectQuizContainerOptionWordInflectionCausativeVerbQuiz = ['使役動詞','使役動詞'];
$str_selectQuizContainerOptionWordInflectionHonorificVerbQuiz = ['尊敬動詞','尊敬動詞'];
$str_selectQuizContainerOptionWordInflectionCausativePassiveVerbQuiz = ['使役受身動詞','使役受身動詞'];
$str_selectQuizContainerOptionPlainform = ['普通形Quiz','普通形Quiz'];
$str_selectQuizContainerOptionPlainformVerbQuiz = ['動詞普通形','動詞普通形'];
$str_selectQuizContainerOptionPlainformIAdjectiveQuiz = ['い形容詞普通形','い形容詞普通形'];
$str_selectQuizContainerOptionPlainformNaAdjectiveQuiz = ['な形容詞普通形','な形容詞普通形'];
$str_selectQuizContainerOptionPlainformNounQuiz = ['名詞普通形','名詞普通形'];
$str_selectQuizContainerOptionGrammarQuiz = ['文法Quiz','文法Quiz'];
$str_selectQuizContainerOptionGrammarQuizBeginner = ['入門','入門'];
$str_selectQuizContainerOptionGrammarQuizBasic = ['基礎','基礎'];
$str_selectQuizContainerOptionGrammarQuizIntermediate = ['初級','初級'];
$str_selectQuizContainerOptionGrammarQuizAdvanced = ['中級','中級'];
$str_selectQuizContainerOptionGrammarQuizExpert = ['上級','上級'];
$str_selectQuizContainerOptionGrammarQuizMaster = ['高級','高級'];
$str_selectQuizContainerOptionSortingQuiz = ['並べ替えQuiz','排序Quiz'];
$str_selectQuizContainerOptionSortingQuizBeginner = ['入門','入門'];
$str_selectQuizContainerOptionSortingQuizBasic = ['基礎','基礎'];
$str_selectQuizContainerOptionSortingQuizIntermediate = ['初級','初級'];
$str_selectQuizContainerOptionSortingQuizAdvanced = ['中級','中級'];
$str_selectQuizContainerOptionSortingQuizExpert = ['上級','上級'];
$str_selectQuizContainerOptionSortingQuizMaster = ['高級','高級'];

$arr_selectQuizContainerOption_landing = [
    'japaneseParticle' => [
        'title' => $str_selectQuizContainerOptionGroupJapaneseParticle,
        'items' => [
            [
                'title' => 'japaneseParticleBeginner',
                'level' => $int_mastery_level_jws_beginner,
                'label' => $str_selectQuizContainerOptionJapaneseParticleQuizBeginner
            ],
            [
                'title' => 'japaneseParticleBasic',
                'level' => $int_mastery_level_jws_basic,
                'label' => $str_selectQuizContainerOptionJapaneseParticleQuizBasic
            ],
            [
                'title' => 'japaneseParticleIntermediate',
                'level' => $int_mastery_level_jws_intermediate,
                'label' => $str_selectQuizContainerOptionJapaneseParticleQuizIntermediate
            ],
            [
                'title' => 'japaneseParticleAdvanced',
                'level' => $int_mastery_level_jws_advanced,
                'label' => $str_selectQuizContainerOptionJapaneseParticleQuizAdvanced
            ],
            [
                'title' => 'japaneseParticleExpert',
                'level' => $int_mastery_level_jws_expert,
                'label' => $str_selectQuizContainerOptionJapaneseParticleQuizExpert
            ],
            [
                'title' => 'japaneseParticleMaster',
                'level' => $int_mastery_level_jws_master,
                'label' => $str_selectQuizContainerOptionJapaneseParticleQuizMaster
            ],
        ]
    ],
    'wordInflection' => [
        'title' => $str_selectQuizContainerOptionGroupWordInflection,
        'items' => [
            [
                'title' => 'wordInflectionVerbDictionary',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionVerbDictionaryQuiz
            ],
            [
                'title' => 'wordInflectionVerbNai',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionVerbNaiQuiz
            ],
            [
                'title' => 'wordInflectionVerbTe',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionVerbTeQuiz
            ],
            [
                'title' => 'wordInflectionVerbTa',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionVerbTaQuiz
            ],
            [
                'title' => 'wordInflectionVerbBa',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionVerbBaQuiz
            ],
            [
                'title' => 'wordInflectionVerbVolitional',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionVerbVolitionalQuiz
            ],
            [
                'title' => 'wordInflectionVerbImperative',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionVerbImperativeQuiz
            ],
            [
                'title' => 'wordInflectionPotentialVerb',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionPotentialVerbQuiz
            ],
            [
                'title' => 'wordInflectionPassiveVerb',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionPassiveVerbQuiz
            ],
            [
                'title' => 'wordInflectionCausativeVerb',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionCausativeVerbQuiz
            ],
            [
                'title' => 'wordInflectionHonorificVerb',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionHonorificVerbQuiz
            ],
            [
                'title' => 'wordInflectionCausativePassiveVerb',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionCausativePassiveVerbQuiz
            ],
        ]
    ],
    'plainform' => [
        'title' => $str_selectQuizContainerOptionGroupPlainform,
        'items' => [
            [
                'title' => 'plainformVerb',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionPlainformVerbQuiz
            ],
            [
                'title' => 'plainformIAdjective',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionPlainformIAdjectiveQuiz
            ],
            [
                'title' => 'plainformNaAdjective',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionPlainformNaAdjectiveQuiz
            ],
            [
                'title' => 'plainformNoun',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionPlainformNounQuiz
            ],
        ]
    ],
    'grammar' => [
        'title' => $str_selectQuizContainerOptionGroupGrammar,
        'items' => [
            [
                'title' => 'grammarBeginner',
                'level' => $int_mastery_level_jws_beginner,
                'label' => $str_selectQuizContainerOptionGrammarQuizBeginner
            ],
            [
                'title' => 'grammarBasic',
                'level' => $int_mastery_level_jws_basic,
                'label' => $str_selectQuizContainerOptionGrammarQuizBasic
            ],
            [
                'title' => 'grammarIntermediate',
                'level' => $int_mastery_level_jws_intermediate,
                'label' => $str_selectQuizContainerOptionGrammarQuizIntermediate
            ],
            [
                'title' => 'grammarAdvanced',
                'level' => $int_mastery_level_jws_advanced,
                'label' => $str_selectQuizContainerOptionGrammarQuizAdvanced
            ],
            [
                'title' => 'grammarExpert',
                'level' => $int_mastery_level_jws_expert,
                'label' => $str_selectQuizContainerOptionGrammarQuizExpert
            ],
            [
                'title' => 'grammarMaster',
                'level' => $int_mastery_level_jws_master,
                'label' => $str_selectQuizContainerOptionGrammarQuizMaster
            ],
        ]
    ],
    'sorting' => [
        'title' => $str_selectQuizContainerOptionGroupSortingQuiz,
        'items' => [
            [
                'title' => 'sortingBeginner',
                'level' => $int_mastery_level_jws_beginner,
                'label' => $str_selectQuizContainerOptionSortingQuizBeginner
            ],
            [
                'title' => 'sortingBasic',
                'level' => $int_mastery_level_jws_basic,
                'label' => $str_selectQuizContainerOptionSortingQuizBasic
            ],
            [
                'title' => 'sortingIntermediate',
                'level' => $int_mastery_level_jws_intermediate,
                'label' => $str_selectQuizContainerOptionSortingQuizIntermediate
            ],
            [
                'title' => 'sortingAdvanced',
                'level' => $int_mastery_level_jws_advanced,
                'label' => $str_selectQuizContainerOptionSortingQuizAdvanced
            ],
            [
                'title' => 'sortingExpert',
                'level' => $int_mastery_level_jws_expert,
                'label' => $str_selectQuizContainerOptionSortingQuizExpert
            ],
            [
                'title' => 'sortingMaster',
                'level' => $int_mastery_level_jws_master,
                'label' => $str_selectQuizContainerOptionSortingQuizMaster
            ],
        ]
    ]
];

$arr_selectQuizContainerOption_wise = [
    'japaneseParticle' => [
        'title' => $str_selectQuizContainerOptionGroupJapaneseParticle,
        'items' => [
            [
                'title' => 'japaneseParticle',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionJapaneseParticleQuiz
            ]
        ]
    ],
    'wordInflection' => [
        'title' => $str_selectQuizContainerOptionGroupWordInflection,
        'items' => [
            [
                'title' => 'wordInflection',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionWordInflectionQuiz
            ]
        ]
    ],
    'plainform' => [
        'title' => $str_selectQuizContainerOptionGroupPlainform,
        'items' => [
            [
                'title' => 'plainform',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionPlainform
            ]
        ]
    ],
    'grammar' => [
        'title' => $str_selectQuizContainerOptionGroupGrammar,
        'items' => [
            [
                'title' => 'grammar',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionGrammarQuiz
            ]
        ]
    ],
    'sorting' => [
        'title' => $str_selectQuizContainerOptionGroupSortingQuiz,
        'items' => [
            [
                'title' => 'sorting',
                'level' => $int_mastery_level_select_all,
                'label' => $str_selectQuizContainerOptionSortingQuiz
            ]
        ]
    ]
];


$str_sortingQuizPieceListContainerLiButtonsInflection = ['change','change'];
$str_sortingQuizPieceListContainerLiButtonsKana = ['かな','平假名'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
$str_selectSortingQuizTitle = ['Select Quiz','Select Quiz'];
$str_selectSortingQuizButtonNext = ['次','下一個'];
$str_selectSortingQuizButtonAgain = ['最初から','重新開始'];
$str_selectSortingQuizCoutionAllSeen = ['すべての問題が表示されました','顯示完了所有Quiz'];
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー
// ーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーーー


// quizの変数
$int_quiz_japaneseParticleQuiz = STATUS_FIRST;
$int_quiz_wordInflectionQuiz = STATUS_SECOND;
$int_quiz_grammarQuiz = STATUS_THIRD;
$int_quiz_plainformQuiz = STATUS_FOURTH;
$int_quiz_sortingQuiz = STATUS_FIFTH;

$str_quiz_japaneseParticleQuiz ='japaneseParticle';
$str_quiz_wordInflectionQuiz = 'wordInflection';
$str_quiz_grammarQuiz = 'grammar';
$str_quiz_plainformQuiz = 'plainform';
$str_quiz_sortingQuiz = 'sorting';

$arr_quiz_data = [
	$int_quiz_japaneseParticleQuiz =>[
		'quiz_type' => $str_quiz_japaneseParticleQuiz,
		'quiz_title' => $str_japaneseParticleQuizTitle,
		'quiz_path' => $path_japanese_particle_quiz,
		'main_section_id' => 'japaneseParticleQuizMainSection',
		'quizButtonToPage' => 'japaneseParticleQuizButtonToPage',
		'quizButtonNextQuestion' => 'japaneseParticleQuizButtonNextQuestion',
		'quizButtonHint' => 'japaneseParticleQuizButtonHint',
		'quizButtonCopy' => 'japaneseParticleQuizButtonCopy'
	],
	$int_quiz_wordInflectionQuiz =>[
		'quiz_type' => $str_quiz_wordInflectionQuiz,
		'quiz_title' => $str_wordInflectionQuizTitle,
		'quiz_path' => $path_word_inflection_quiz,
		'main_section_id' => 'wordInflectionQuizMainSection',
		'quizButtonToPage' => 'wordInflectionQuizButtonToPage',
		'quizButtonNextQuestion' => 'wordInflectionQuizButtonNextQuestion',
		'quizButtonHint' => 'wordInflectionQuizButtonHint',
		'quizButtonCopy' => 'wordInflectionQuizButtonCopy'
	],
	$int_quiz_grammarQuiz =>[
		'quiz_type' => $str_quiz_grammarQuiz,
		'quiz_title' => $str_grammarQuizTitle,
		'quiz_path' => $path_grammar_quiz,
		'main_section_id' => 'grammarQuizMainSection',
		'quizButtonToPage' => 'grammarQuizButtonToPage',
		'quizButtonNextQuestion' => 'grammarQuizButtonNextQuestion',
		'quizButtonHint' => 'grammarQuizButtonHint',
		'quizButtonCopy' => 'grammarQuizButtonCopy'
	],
	$int_quiz_plainformQuiz =>[
		'quiz_type' => $str_quiz_plainformQuiz,
		'quiz_title' => $str_plainformQuizTitle,
		'quiz_path' => $path_plainform_quiz,
		'main_section_id' => 'plainformQuizMainSection',
		'quizButtonToPage' => 'plainformQuizButtonToPage',
		'quizButtonNextQuestion' => 'plainformQuizButtonNextQuestion',
		'quizButtonHint' => 'plainformQuizButtonHint',
		'quizButtonCopy' => 'plainformQuizButtonCopy'
	],
	$int_quiz_sortingQuiz =>[
		'quiz_type' => $str_quiz_sortingQuiz,
		'quiz_title' => $str_sortingQuizTitle,
		'quiz_path' => $path_sorting_quiz,
		'main_section_id' => 'sortingQuizMainSection',
		'quizButtonToPage' => 'sortingQuizButtonToPage',
		'quizButtonNextQuestion' => 'sortingQuizButtonNextQuestion',
		'quizButtonHint' => 'sortingQuizButtonHint',
		'quizButtonCopy' => 'sortingQuizButtonCopy'
	]
];