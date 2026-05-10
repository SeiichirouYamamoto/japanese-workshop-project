<?php


/******************************************************
 *  inflection
 *  
 ******************************************************/

$int_affirmativeNotPastTense = STATUS_FIRST;
$int_negativeNotPastTense = STATUS_SECOND;
$int_affirmativePastTense = STATUS_THIRD;
$int_negativePastTense = STATUS_FOURTH;

$int_masta_japanese_root_id_affirmativeNotPastTense = 12257;
$int_masta_japanese_root_id_negativeNotPastTense = 12258;
$int_masta_japanese_root_id_affirmativePastTense = 12259;
$int_masta_japanese_root_id_negativePastTense = 12260;

$arr_form_list_default = [
	$int_masta_japanese_root_id_affirmativeNotPastTense,
	$int_masta_japanese_root_id_negativeNotPastTense,
	$int_masta_japanese_root_id_affirmativePastTense,
	$int_masta_japanese_root_id_negativePastTense
];

$arr_affirmative_negative_tense = [
	$int_affirmativeNotPastTense => ['肯定','肯定'],
	$int_negativeNotPastTense => ['否定','否定'],
	$int_affirmativePastTense => ['過去','過去'],
	$int_negativePastTense => ['過去否定','過去否定']
];

$int_masta_japanese_root_id_predicate_noun = 26720;
$int_masta_japanese_root_id_predicate_verb = 26721;
$int_masta_japanese_root_id_predicate_i_adj = 26722;
$int_masta_japanese_root_id_predicate_na_adj = 26723;

$arr_predicate_ids = [
	$int_masta_japanese_root_id_predicate_noun,
	$int_masta_japanese_root_id_predicate_verb,
	$int_masta_japanese_root_id_predicate_i_adj,
	$int_masta_japanese_root_id_predicate_na_adj
];

$int_masta_japanese_root_id_noun_mod_by_noun = 26726;
$int_masta_japanese_root_id_noun_mod_by_verb = 255;
$int_masta_japanese_root_id_noun_mod_by_i_adj = 26728;
$int_masta_japanese_root_id_noun_mod_by_na_adj = 26727;
$int_masta_japanese_root_id_verb_mod = 26740;
$int_masta_japanese_root_id_adj_mod = 26741;

$arr_special_term_ids = [
	$int_masta_japanese_root_id_noun_mod_by_noun,
	$int_masta_japanese_root_id_noun_mod_by_verb,
	$int_masta_japanese_root_id_noun_mod_by_i_adj,
	$int_masta_japanese_root_id_noun_mod_by_na_adj,
	$int_masta_japanese_root_id_verb_mod,
	$int_masta_japanese_root_id_adj_mod
];


$int_PoliteFormAffirmativeNotPastTense = 1;
$int_PoliteFormNegativeNotPastTense = 2;
$int_PoliteFormAffirmativePastTense = 3;
$int_PoliteFormNegativePastTense = 4;
$int_OriginalForm = 5;
$int_NaiForm = 6;
$int_TaForm = 7;
$int_NakattaForm = 8;
$int_DictionaryForm = 9;
$int_TeForm = 10;
$int_NakuteForm = 11;
$int_NaideForm = 12;
$int_TeFormShortened = 13;
$int_BaForm = 14;
$int_NakerebaForm = 15;
$int_baform_shortened = 16;
$int_NakerebaFormShortenedNakya = 17;
$int_NakerebaFormShortenedNakerya = 18;
$int_TaraForm = 19;
$int_NakattaraForm = 20;
$int_TariForm = 21;
$int_NakattariForm = 22;
$int_MasuForm = 23;
$int_ImperfectiveForm = 24;
$int_ImperfectiveForm_se = 25;
$int_VolitionalForm = 26;
$int_VolitionalFormShortened = 27;
$int_ImperativeForm = 28;
$int_ProhibitionForm = 29;
$int_StemForm = 30;
$int_NaiFormStemForm = 31;
$int_AdverbForm = 32;
$int_AdnominalModificationFormNO = 33;
$int_AdnominalModificationFormNA = 34;
$int_PotentialVerb = 35;
$int_PassiveVerb = 36;
$int_CausativeVerb = 37;
$int_HonorificVerb = 38;
$int_CausativePassiveVerb = 39;
$int_CausativeVerbShortened = 40;
$int_CausativePassiveVerbShortened = 41;

//五段・カ行イ音便
$int_V1KU = 2;
//五段・ガ行
$int_V1GU = 3;
//五段・サ行
$int_V1SA = 4;
//五段・タ行
$int_V1TA = 5;
//五段・ナ行
$int_V1NA = 6;
//五段・バ行
$int_V1BA = 7;
//五段・マ行
$int_V1MA = 8;
//五段・ラ行
$int_V1RA = 9;
//五段・ワ行促音便
$int_V1WA = 10;
//五段・カ行促音便(行く)
$int_V1KU2 = 11;
//五段・ラ行特殊(くださる)
$int_V1RA2 = 12;
//五段・ある
$int_V1ARU = 13;
//第ニ類(イ段)
$int_V2I = 14;
//第二類(エ段)
$int_V2E = 15;
//第三類(来る)
$int_V3K = 16;
//第三類(する)
$int_V3S = 17;
//サ変・−スル
$int_V3S2 = 18;
//サ変・−ズル
$int_V3Z = 19;
//い形容詞
$int_AI = 20;
//い形容詞
$int_AI2 = 21;
//な形容詞
$int_ANA = 22;
//副詞性な形容詞
$int_ANAToTaru = 23;
//名詞
$int_N = 24;
//副詞性名詞
$int_NAdV = 25;
//副詞
$int_AdV = 26;
//連体詞
$int_PNA = 27;
//接続詞
$int_C = 28;
//助動詞い型
$int_AuxVI = 29;
//助動詞な型
$int_AuxVNA = 30;
//助詞
$int_JapaneseParticle = 31;
//感動詞
$int_Interjection = 32;
//接頭辞
$int_Prefix = 33;
//接尾辞
$int_Suffix = 34;
//助数詞
$int_CounterWord = 35;
//慣用句
$int_Idiom = 36;
//記号
$int_Symbol = 37;
//数字
$int_Num = 38;


$int_stem_others = STATUS_FIRST;
$int_stem_a = 1;
$int_stem_i = 2;
$int_stem_u = 3;
$int_stem_e = 4;
$int_stem_o = 5;


$verb_plain_form = STATUS_FIRST;
$verb_polite_form = STATUS_SECOND;

$int_inflection_add_part_te = STATUS_FIRST;
$int_inflection_add_part_ta = STATUS_SECOND;

$int_inflection_add_part_shi = STATUS_FIRST;
$int_inflection_add_part_se = STATUS_SECOND;

$int_inflection_add_part_Normal = STATUS_FIRST;
$int_inflection_add_part_shortened = STATUS_SECOND;

$int_inflection_add_part_CausativeVerb = STATUS_FIRST;
$int_inflection_add_part_CausativePassiveVerb = STATUS_SECOND;

$arr_inflection_ending = [
	$int_V1KU=>[
		$int_stem_a=>'か',
		$int_stem_i=>'き',
		$int_stem_u=>'く',
		$int_stem_e=>'け',
		$int_stem_o=>'こ'
	],
	$int_V1GU=>[
		$int_stem_a=>'が',
		$int_stem_i=>'ぎ',
		$int_stem_u=>'ぐ',
		$int_stem_e=>'げ',
		$int_stem_o=>'ご'
	],
	$int_V1SA=>[
		$int_stem_a=>'さ',
		$int_stem_i=>'し',
		$int_stem_u=>'す',
		$int_stem_e=>'せ',
		$int_stem_o=>'そ'
	],
	$int_V1TA=>[
		$int_stem_a=>'た',
		$int_stem_i=>'ち',
		$int_stem_u=>'つ',
		$int_stem_e=>'て',
		$int_stem_o=>'と'
	],
	$int_V1NA=>[
		$int_stem_a=>'な',
		$int_stem_i=>'に',
		$int_stem_u=>'ぬ',
		$int_stem_e=>'ね',
		$int_stem_o=>'の'
	],
	$int_V1BA=>[
		$int_stem_a=>'ば',
		$int_stem_i=>'び',
		$int_stem_u=>'ぶ',
		$int_stem_e=>'べ',
		$int_stem_o=>'ぼ'
	],
	$int_V1MA=>[
		$int_stem_a=>'ま',
		$int_stem_i=>'み',
		$int_stem_u=>'む',
		$int_stem_e=>'め',
		$int_stem_o=>'も'
	],
	$int_V1RA=>[
		$int_stem_a=>'ら',
		$int_stem_i=>'り',
		$int_stem_u=>'る',
		$int_stem_e=>'れ',
		$int_stem_o=>'ろ'
	],
	$int_V1WA=>[
		$int_stem_a=>'わ',
		$int_stem_i=>'い',
		$int_stem_u=>'う',
		$int_stem_e=>'え',
		$int_stem_o=>'お'
	],
	$int_V1KU2=>[
		$int_stem_a=>'か',
		$int_stem_i=>'き',
		$int_stem_u=>'く',
		$int_stem_e=>'け',
		$int_stem_o=>'こ'
	],
	$int_V1RA2=>[
		$int_stem_a=>'ら',
		$int_stem_i=>'り',
		$int_stem_u=>'る',
		$int_stem_e=>'れ',
		$int_stem_o=>'ろ'
	],
	$int_V1ARU=>[
		$int_stem_a=>'ら',
		$int_stem_i=>'り',
		$int_stem_u=>'る',
		$int_stem_e=>'れ',
		$int_stem_o=>'ろ'
	]
];

$str_inflection_IA_ending_cc = '良';
$str_inflection_IA_ending_hi = 'い';
$str_inflection_IA_ending_hy = 'よ';

$str_inflected_label_inflection_process = 'inflection_process';
$str_stages_of_inflection_get_baseform_japanese = 'baseformJapanese';
$str_stages_of_inflection_get_baseform_kana = 'baseformKana';


$arr_how_to_make_masuform = [
	['「る」を消す','「る」拿掉'],
	['「ます」を消す','「ます」拿掉']
];

$arr_how_to_make_masuform_V2 = [
	['「る」を消す','「る」拿掉'],
	['「ます」を消す','「ます」拿掉']
];
$arr_how_to_make_masuform_V1RA2 = [
	['※「～る」→「～い」','※「～る」→「～い」'],
	['「ます」を消す','「ます」拿掉']
];
$arr_how_to_make_masuform_V3K = [
	['「来る」→「来 (き)」','「来る」→「来 (き)」'],
	['「ます」を消す','「ます」拿掉']
];
$arr_how_to_make_masuform_V3S = [
	['「する」→「し」','「する」→「し」'],
	['「ます」を消す','「ます」拿掉']
];
$arr_how_to_make_masuform_V3Z = [
	['「ずる」→「じ」','「ずる」→「じ」'],
	['「ます」を消す','「ます」拿掉']
];

$arr_how_to_make_dictionaryform_V2 = [
	[],
	['「る」を加える','加上「る」']
];
$arr_how_to_make_dictionaryform_V3K = [
	[],
	['「来ます」→「来る (くる)」','「来ます」→「来る (くる)」']
];
$arr_how_to_make_dictionaryform_V3S = [
	[],
	['「します」→「する」','「します」→「する」']
];
$arr_how_to_make_dictionaryform_V3Z = [
	[],
	['「じます」→「ずる」','「じます」→「ずる」']
];

$arr_how_to_make_imperfectiveform_V1ARU = [
	['※「ある」を消す','※「ある」拿掉'],
	['※「あります」を消す','※「あります」拿掉']
];
$arr_how_to_make_imperfectiveform_V3K = [
	['「来る」→「来 (こ)」','「来る」→「来 (こ)」'],
	['「来ます」→「来 (こ)」','「来ます」→「来 (こ)」']
];
$arr_how_to_make_imperfectiveform_V3S = [
	['「する」→「し」','「する」→「し」'],
	['「します」→「し」','「します」→「し」']
];
$arr_how_to_make_imperfectiveform_V3SE = [
	['「する」→「せ」','「する」→「せ」'],
	['「します」→「せ」','「します」→「せ」']
];
$arr_how_to_make_imperfectiveform_V3S2 = [
	['「する」→「さ」','「する」→「さ」'],
	['「します」→「さ」','「します」→「さ」']
];
$arr_how_to_make_imperfectiveform_V3Z = [
	['「ずる」→「じ」','「ずる」→「じ」'],
	['「じます」→「じ」','「じます」→「じ」']
];

$arr_how_to_make_teform_V2 = [
	['「て」を加える','加上「て」'],
	['「て」を加える','加上「て」']
];
$arr_how_to_make_teform_V3K = [
	['「来る」→「来て (きて)」','「来る」→「来て (きて)」'],
	['「来ます」→「来て (きて)」','「来ます」→「来て (きて)」']
];
$arr_how_to_make_teform_V3S = [
	['「する」→「して」','「する」→「して」'],
	['「します」→「して」','「します」→「して」']
];
$arr_how_to_make_teform_V3Z = [
	['「ずる」→「じて」','「ずる」→「じて」'],
	['「じます」→「じて」','「じます」→「じて」']
];
$arr_how_to_make_teform_V1Sokuonbin = [
	['「う・つ・る」→「って」','「う・つ・る」→「って」'],
	['「い・ち・り」→「って」','「い・ち・り」→「って」']
];
$arr_how_to_make_teform_V1Hatsuonbin = [
	['「ぬ・ぶ・む」→「んで」','「ぬ・ぶ・む」→「んで」'],
	['「に・び・み」→「んで」','「に・び・み」→「んで」']
];
$arr_how_to_make_teform_V1IonbinKI = [
	['「く」→「いて」','「く」→「いて」'],
	['「き」→「いて」','「き」→「いて」']
];
$arr_how_to_make_teform_V1IonbinGI = [
	['「ぐ」→「いで」','「ぐ」→「いで」'],
	['「ぎ」→「いで」','「ぎ」→「いで」']
];
$arr_how_to_make_teform_V1S = [
	['「す」→「して」','「す」→「して」'],
	['「し」→「して」','「し」→「して」']
];
$arr_how_to_make_teform_V1IKU = [
	['「行く」→「行って」','「行く」→「行って」'],
	['「行き」→「行って」','「行き」→「行って」']
];
$arr_how_to_make_teform_shortened = [
	['「て / で」を消す','省略「て / で」'],
	['「て / で」を消す','省略「て / で」']
];

$arr_how_to_make_taform_V2 = [
	['「た」を加える','加上「た」'],
	['「た」を加える','加上「た」']
];
$arr_how_to_make_taform_V3K = [
	['「来る」→「来て (きた)」','「来る」→「来て (きた)」'],
	['「来ます」→「来た (きた)」','「来ます」→「来た (きた)」']
];
$arr_how_to_make_taform_V3S = [
	['「する」→「した」','「する」→「した」'],
	['「します」→「した」','「します」→「した」']
];
$arr_how_to_make_taform_V3Z = [
	['「ずる」→「じた」','「ずる」→「じた」'],
	['「じます」→「じた」','「じます」→「じた」']
];
$arr_how_to_make_taform_V1Sokuonbin = [
	['「う・つ・る」→「った」','「う・つ・る」→「った」'],
	['「い・ち・り」→「った」','「い・ち・り」→「った」']
];
$arr_how_to_make_taform_V1Hatsuonbin = [
	['「ぬ・ぶ・む」→「んだ」','「ぬ・ぶ・む」→「んだ」'],
	['「に・び・み」→「んだ」','「に・び・み」→「んだ」']
];
$arr_how_to_make_taform_V1IonbinKI = [
	['「く」→「いた」','「く」→「いた」'],
	['「き」→「いた」','「き」→「いた」']
];
$arr_how_to_make_taform_V1IonbinGI = [
	['「ぐ」→「いだ」','「ぐ」→「いだ」'],
	['「ぎ」→「いだ」','「ぎ」→「いだ」']
];
$arr_how_to_make_taform_V1S = [
	['「す」→「した」','「す」→「した」'],
	['「し」→「した」','「し」→「した」']
];
$arr_how_to_make_taform_V1IKU = [
	['「行く」→「行った」','「行く」→「行った」'],
	['「行き」→「行った」','「行き」→「行った」']
];

$arr_how_to_make_baform_V2 = [
	['「れば」を加える','加上「れば」'],
	['「れば」を加える','加上「れば」']
];
$arr_how_to_make_baform_V3K = [
	['「来る」→「来れば (くれば)」','「来る」→「来れば (くれば)」'],
	['「来ます」→「来れば (くれば)」','「来ます」→「来れば (くれば)」']
];
$arr_how_to_make_baform_V3S = [
	['「する」→「すれば」','「する」→「すれば」'],
	['「します」→「すれば」','「します」→「すれば」']
];
$arr_how_to_make_baform_V3Z = [
	['「ずる」→「ずれば」','「ずる」→「ずれば」'],
	['「じます」→「ずれば」','「じます」→「ずれば」']
];
$arr_how_to_make_baform_V1 = [
	['「ば」を加える','加上「ば」'],
	['「ば」を加える','加上「ば」']
];

$arr_how_to_make_baform_shortened_V2 = [
	['「りゃ」を加える','加上「りゃ」'],
	['「りゃ」を加える','加上「りゃ」']
];
$arr_how_to_make_baform_shortened_V3K = [
	['「来る」→「来りゃ (くりゃ)」','「来る」→「来りゃ (くりゃ)」'],
	['「来ます」→「来りゃ (くりゃ)」','「来ます」→「来りゃ (くりゃ)」']
];
$arr_how_to_make_baform_shortened_V3S = [
	['「する」→「すりゃ」','「する」→「すりゃ」'],
	['「します」→「すりゃ」','「します」→「すりゃ」']
];
$arr_how_to_make_baform_shortened_V3S2 = [
	['「する」→「しゃ」','「する」→「しゃ」'],
	['「します」→「しゃ」','「します」→「しゃ」']
];
$arr_how_to_make_baform_shortened_V3Z = [
	['「ずる」→「じりゃ」','「ずる」→「じりゃ」'],
	['「じます」→「じりゃ」','「じます」→「じりゃ」']
];
$arr_how_to_make_baform_shortened_V1 = [
	['「ゃ」を加える','加上「ゃ」'],
	['「ゃ」を加える','加上「ゃ」']
];

$arr_how_to_make_volitionalform_V2 = [
	['「よう」を加える','加上「よう」'],
	['「よう」を加える','加上「よう」']
];
$arr_how_to_make_volitionalform_V3K = [
	['「来る」→「来よう (こよう)」','「来る」→「来よう (こよう)」'],
	['「来ます」→「来よう (こよう)」','「来ます」→「来よう (こよう)」']
];
$arr_how_to_make_volitionalform_V3S = [
	['「する」→「しよう」','「する」→「しよう」'],
	['「します」→「しよう」','「します」→「しよう」']
];
$arr_how_to_make_volitionalform_V3S2 = [
	['「する」→「そう」','「する」→「そう」'],
	['「します」→「そう」','「します」→「そう」']
];
$arr_how_to_make_volitionalform_V3Z = [
	['「ずる」→「じよう」','「ずる」→「じよう」'],
	['「じます」→「じよう」','「じます」→「じよう」']
];
$arr_how_to_make_volitionalform_V1 = [
	['「う」を加える','加上「う」'],
	['「う」を加える','加上「う」']
];
$arr_how_to_make_volitionalform_shortened = [
	['「う」を消す','「う」拿掉'],
	['「う」を消す','「う」拿掉']
];

$arr_how_to_make_imperativeform_V2 = [
	['「ろ」を加える','加上「ろ」'],
	['「ろ」を加える','加上「ろ」']
];
$arr_how_to_make_imperativeform_V3K = [
	['「来る」→「来い (こい)」','「来る」→「来い (こい)」'],
	['「来ます」→「来い (こい)」','「来ます」→「来い (こい)」']
];
$arr_how_to_make_imperativeform_V3S = [
	['「する」→「しろ」','「する」→「しろ」'],
	['「します」→「しろ」','「します」→「しろ」']
];
$arr_how_to_make_imperativeform_V3S2 = [
	['「する」→「せ」','「する」→「せ」'],
	['「します」→「せ」','「します」→「せ」']
];
$arr_how_to_make_imperativeform_V3Z = [
	['「ずる」→「じろ」','「ずる」→「じろ」'],
	['「じます」→「じろ」','「じます」→「じろ」']
];

$arr_how_to_make_potentialverb_V2 = [
	['「られる」を加える','加上「られる」'],
	['「られる」を加える','加上「られる」']
];
$arr_how_to_make_potentialverb_V3K = [
	['「来る」→「来られる (こられる)」','「来る」→「来られる (こられる)」'],
	['「来ます」→「来られる (こられる)」','「来ます」→「来られる (こられる)」']
];
$arr_how_to_make_potentialverb_V3S = [
	['「する」→「できる」','「する」→「できる」'],
	['「します」→「できる」','「します」→「できる」']
];
$arr_how_to_make_potentialverb_V3S2 = [
	['「する」→「せる」','「する」→「せる」'],
	['「します」→「せる」','「します」→「せる」']
];
$arr_how_to_make_potentialverb_V3Z = [
	['「ずる」→「じられる」','「ずる」→「じられる」'],
	['「じます」→「じられる」','「じます」→「じられる」']
];
$arr_how_to_make_potentialverb_V1 = [
	['「る」を加える','加上「る」'],
	['「る」を加える','加上「る」']
];

$arr_how_to_make_passive_honorificverb_V2 = [
	['「られる」を加える','加上「られる」'],
	['「られる」を加える','加上「られる」']
];
$arr_how_to_make_passive_honorificverb_V3K = [
	['「来る」→「来られる (こられる)」','「来る」→「来られる (こられる)」'],
	['「来ます」→「来られる (こられる)」','「来ます」→「来られる (こられる)」']
];
$arr_how_to_make_passive_honorificverb_V3S = [
	['「する」→「される」','「する」→「される」'],
	['「します」→「される」','「します」→「される」']
];
$arr_how_to_make_passive_honorificverb_V3S2 = [
	['「する」→「される」','「する」→「される」'],
	['「します」→「される」','「します」→「される」']
];
$arr_how_to_make_passive_honorificverb_V3Z = [
	['「ずる」→「じられる」','「ずる」→「じられる」'],
	['「じます」→「じられる」','「じます」→「じられる」']
];
$arr_how_to_make_passive_honorificverb_V1 = [
	['「れる」を加える','加上「れる」'],
	['「れる」を加える','加上「れる」']
];

$arr_how_to_make_causativeverb_V2 = [
	['「させる」を加える','加上「させる」'],
	['「させる」を加える','加上「させる」']
];
$arr_how_to_make_causativeverb_V3K = [
	['「来る」→「来させる (こさせる)」','「来る」→「来させる (こさせる)」'],
	['「来ます」→「来させる (こさせる)」','「来ます」→「来させる (こさせる)」']
];
$arr_how_to_make_causativeverb_V3S = [
	['「する」→「させる」','「する」→「させる」'],
	['「します」→「させる」','「します」→「させる」']
];
$arr_how_to_make_causativeverb_V3S2 = [
	['「する」→「させる」','「する」→「させる」'],
	['「します」→「させる」','「します」→「させる」']
];
$arr_how_to_make_causativeverb_V3Z = [
	['「ずる」→「じさせる」','「ずる」→「じさせる」'],
	['「じます」→「じさせる」','「じます」→「じさせる」']
];
$arr_how_to_make_causativeverb_V1 = [
	['「せる」を加える','加上「せる」'],
	['「せる」を加える','加上「せる」']
];


$arr_how_to_make_causativepassiveverb_V2 = [
	['「させられる」を加える','加上「させられる」'],
	['「させられる」を加える','加上「させられる」']
];
$arr_how_to_make_causativepassiveverb_V3K = [
	['「来る」→「来させる (こさせられる)」','「来る」→「来させる (こさせられる)」'],
	['「来ます」→「来させる (こさせられる)」','「来ます」→「来させる (こさせられる)」']
];
$arr_how_to_make_causativepassiveverb_V3S = [
	['「する」→「させられる」','「する」→「させられる」'],
	['「します」→「させられる」','「します」→「させられる」']
];
$arr_how_to_make_causativepassiveverb_V3S2 = [
	['「する」→「させられる」','「する」→「させられる」'],
	['「します」→「させられる」','「します」→「させられる」']
];
$arr_how_to_make_causativepassiveverb_V3Z = [
	['「ずる」→「じさせられる」','「ずる」→「じさせられる」'],
	['「じます」→「じさせられる」','「じます」→「じさせられる」']
];
$arr_how_to_make_causativepassiveverb_V1 = [
	['「せられる」を加える','加上「せられる」'],
	['「せられる」を加える','加上「せられる」']
];


$arr_how_to_make_causativeverb_shortened_V2 = [
	['「さす」を加える','加上「さす」'],
	['「さす」を加える','加上「さす」']
];
$arr_how_to_make_causativeverb_shortened_V3K = [
	['「来る」→「来さす (こさす)」','「来る」→「来さす (こさす)」'],
	['「来ます」→「来さす (こさす)」','「来ます」→「来さす (こさす)」']
];
$arr_how_to_make_causativeverb_shortened_V3S = [
	['「する」→「さす」','「する」→「さす」'],
	['「します」→「さす」','「します」→「さす」']
];
$arr_how_to_make_causativeverb_shortened_V3S2 = [
	['「する」→「さす」','「する」→「さす」'],
	['「します」→「さす」','「します」→「さす」']
];
$arr_how_to_make_causativeverb_shortened_V3Z = [
	['「ずる」→「じさす」','「ずる」→「じさす」'],
	['「じます」→「じさす」','「じます」→「じさす」']
];
$arr_how_to_make_causativeverb_shortened_V1 = [
	['「す」を加える','加上「す」'],
	['「す」を加える','加上「す」']
];


$arr_how_to_make_causativepassiveverb_shortened_V2 = [
	['「さされる」を加える','加上「さされる」'],
	['「さされる」を加える','加上「さされる」']
];
$arr_how_to_make_causativepassiveverb_shortened_V3K = [
	['「来る」→「来さされる (こさされる)」','「来る」→「来さされる (こさされる)」'],
	['「来ます」→「来さされる (こさされる)」','「来ます」→「来さされる (こさされる)」']
];
$arr_how_to_make_causativepassiveverb_shortened_V3S = [
	['「する」→「さされる」','「する」→「さされる」'],
	['「します」→「さされる」','「します」→「さされる」']
];
$arr_how_to_make_causativepassiveverb_shortened_V3S2 = [
	['「する」→「さされる」','「する」→「さされる」'],
	['「します」→「さされる」','「します」→「さされる」']
];
$arr_how_to_make_causativepassiveverb_shortened_V3Z = [
	['「ずる」→「じさされる」','「ずる」→「じさされる」'],
	['「じます」→「じさされる」','「じます」→「じさされる」']
];
$arr_how_to_make_causativepassiveverb_shortened_V1 = [
	['「される」を加える','加上「される」'],
	['「される」を加える','加上「される」']
];


$arr_how_to_make_verb_stem = [
	[
	$int_stem_a=>['「ア段音」(u → a)','「ア段音」(u → a)'],
	$int_stem_i=>['「イ段音」(u → i)','「イ段音」(u → i)'],
	$int_stem_u=>['「ウ段音」(u → u)','「ウ段音」(u → u)'],
	$int_stem_e=>['「エ段音」(u → e)','「エ段音」(u → e)'],
	$int_stem_o=>['「オ段音」(u → o)','「オ段音」(u → o)']
	],
	[
	$int_stem_a=>['「ア段音」(i → a)','「ア段音」(i → a)'],
	$int_stem_i=>['「イ段音」(i → i)','「イ段音」(i → i)'],
	$int_stem_u=>['「ウ段音」(i → u)','「ウ段音」(i → u)'],
	$int_stem_e=>['「エ段音」(i → e)','「エ段音」(i → e)'],
	$int_stem_o=>['「オ段音」(i → o)','「オ段音」(i → o)']
	]
];

$arr_how_to_make_stem_ia = ['「い」を消す','「い」拿掉'];
$arr_how_to_make_stem_ia_i_to_yo = ['「い」を「よ」に変える','「い」改成「よ」'];

$arr_how_to_make_add_last_part = [
	['「','」を加える'],
	['加上「','」']
];
$arr_how_to_make_change_last_part = [
	['「','」に変える'],
	['改成「','」']
];