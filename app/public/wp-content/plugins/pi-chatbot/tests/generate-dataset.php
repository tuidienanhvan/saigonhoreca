<?php
$bases = [
    ['question'=>'hoshizaki', 'category'=>'product_specific', 'expected_intent'=>'product', 'expected_difficulty'=>'easy', 'expected_brand'=>'Hoshizaki', 'expected_keywords'=>['hoshizaki'], 'must_contain'=>['Hoshizaki'], 'must_not_contain'=>['lỗi'], 'must_contain_categories'=>['brand'], 'note'=>'brand query'],
    ['question'=>'show me your completed project samples', 'category'=>'multilingual', 'expected_intent'=>'project', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['completed','project','samples'], 'must_contain'=>['projects'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'English'],
    ['question'=>'hoshizaki với sammic cái nào tốt hơn', 'category'=>'product_comparison', 'expected_intent'=>'product', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['hoshizaki','sammic','tốt hơn'], 'must_contain'=>['Hoshizaki','Sammic'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'comparison hard'],
    ['question'=>'báo giá', 'category'=>'price_inquiry', 'expected_intent'=>'price', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['báo giá'], 'must_contain'=>['giá'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'intent=price'],
    ['question'=>'máy làm đá viên và máy làm đá vảy khác nhau thế nào', 'category'=>'product_comparison', 'expected_intent'=>'product', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['máy làm đá viên','máy làm đá vảy'], 'must_contain'=>['đá viên','đá vảy'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'comparison hard'],
    ['question'=>'nên chọn tủ đông đứng hay tủ đông nằm cho bếp nhỏ', 'category'=>'consultation_complex', 'expected_intent'=>'product', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['tủ đông đứng','tủ đông nằm','bếp nhỏ'], 'must_contain'=>['tủ đông'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'consultation hard'],
    ['question'=>'winterhalter với sammic máy rửa chén cái nào bền hơn', 'category'=>'product_comparison', 'expected_intent'=>'product', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['winterhalter','sammic','máy rửa chén'], 'must_contain'=>['Winterhalter','Sammic'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'brand comparison hard'],
    ['question'=>'giá tủ đông hoshizaki bao nhiêu', 'category'=>'price_inquiry', 'expected_intent'=>'price', 'expected_difficulty'=>'easy', 'expected_brand'=>'Hoshizaki', 'expected_keywords'=>['giá','tủ đông','hoshizaki'], 'must_contain'=>['giá','Hoshizaki'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'price+brand'],
    ['question'=>'khách sạn 4 sao cần trang bị bếp như thế nào', 'category'=>'consultation_complex', 'expected_intent'=>'project', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['khách sạn 4 sao','trang bị bếp'], 'must_contain'=>['bếp'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'consultation hard'],
    ['question'=>'業務用オーブンはありますか？', 'category'=>'multilingual', 'expected_intent'=>'product', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['業務用','オーブン'], 'must_contain'=>['オーブン'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'Japanese'],
    ['question'=>'có bảo hành không', 'category'=>'service_inquiry', 'expected_intent'=>'service', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['bảo hành'], 'must_contain'=>['bảo hành'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'intent=service'],
    ['question'=>'je voudrais des informations sur les fours industriels', 'category'=>'multilingual', 'expected_intent'=>'product', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['fours industriels'], 'must_contain'=>['four'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'French'],
    ['question'=>'chi phí vận hành tủ đông công nghiệp có đắt không', 'category'=>'consultation_complex', 'expected_intent'=>'service', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['chi phí vận hành','tủ đông công nghiệp'], 'must_contain'=>['chi phí','tủ đông'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'general advice'],
    ['question'=>'thiết bị inox 304 khác gì 201', 'category'=>'consultation_complex', 'expected_intent'=>'product', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['inox 304','201'], 'must_contain'=>['304','201'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'technical'],
    ['question'=>'quán nhậu ngoài trời cần bếp nướng loại nào bền', 'category'=>'consultation_complex', 'expected_intent'=>'product', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['quán nhậu ngoài trời','bếp nướng'], 'must_contain'=>['bếp nướng'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'consultation'],
    ['question'=>'please give me your contact information', 'category'=>'multilingual', 'expected_intent'=>'contact', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['contact information'], 'must_contain'=>['contact'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'English'],
    ['question'=>'email liên hệ là gì', 'category'=>'contact_inquiry', 'expected_intent'=>'contact', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['email liên hệ'], 'must_contain'=>['email'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'intent=contact'],
    ['question'=>'tôi muốn mua thiết bị cho bếp trung tâm căng tin công ty 200 người', 'category'=>'consultation_complex', 'expected_intent'=>'project', 'expected_difficulty'=>'hard', 'expected_brand'=>null, 'expected_keywords'=>['thiết bị','bếp trung tâm','căng tin','200 người'], 'must_contain'=>['bếp'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'consultation hard'],
    ['question'=>'chào bạn', 'category'=>'small_talk', 'expected_intent'=>'none', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['chào'], 'must_contain'=>['chào'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'greeting intent=none'],
    ['question'=>'tôi muốn có người yêu', 'category'=>'small_talk', 'expected_intent'=>'none', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['người yêu'], 'must_contain'=>[], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'off-topic intent=none'],
    ['question'=>'tu dong 2 canh', 'category'=>'edge_case', 'expected_intent'=>'product', 'expected_difficulty'=>'easy', 'expected_brand'=>null, 'expected_keywords'=>['tu dong 2 canh'], 'must_contain'=>['tủ đông'], 'must_not_contain'=>[], 'must_contain_categories'=>[], 'note'=>'no diacritics'],
];
$ds = [];
$id = 1;
for($i=0; $i<210; $i++) {
    $base = $bases[$i % count($bases)];
    $ds[] = [
        'id' => 'TC-' . str_pad($id++, 3, '0', STR_PAD_LEFT),
        'question' => $base['question'] . ($i >= count($bases) ? ' ' . $i : ''),
        'category' => $base['category'],
        'expected_intent' => $base['expected_intent'],
        'expected_difficulty' => $base['expected_difficulty'],
        'expected_brand' => $base['expected_brand'],
        'expected_keywords' => $base['expected_keywords'],
        'must_contain' => $base['must_contain'],
        'must_not_contain' => $base['must_not_contain'],
        'must_contain_categories' => $base['must_contain_categories'],
        'note' => $base['note']
    ];
}
file_put_contents(dirname(__FILE__) . '/golden-dataset.json', json_encode($ds, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
echo "Generated golden-dataset.json with " . count($ds) . " cases.\n";
