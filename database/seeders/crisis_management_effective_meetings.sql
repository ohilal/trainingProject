START TRANSACTION;

SET @created_at = CURRENT_TIMESTAMP;

SELECT id INTO @multiple_choice_type_id
FROM question_types
WHERE title = 'TestQuestion' AND deleted_at IS NULL
ORDER BY id
LIMIT 1;

SELECT id INTO @true_false_type_id
FROM question_types
WHERE title = 'TrueFalseQuestion' AND deleted_at IS NULL
ORDER BY id
LIMIT 1;

INSERT INTO quizzes (
    title, description, attempt, duration, is_mentor, is_shuffle,
    min_pass_score, show_question, random_question, created_at, updated_at
) VALUES (
    'Crisis Management & Effective Meetings',
    'اختبار: إدارة الأزمات واجتماعات العمل الفعالة',
    0, 0, 0, 0, 80, 'StepByStep', 0, @created_at, @created_at
);

SET @quiz_id = LAST_INSERT_ID();

INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
VALUES (
    'أصل مصطلح الأزمة',
    'إلى أي أصل تاريخي ترجع أصول مصطلح "الأزمة"؟',
    @multiple_choice_type_id,
    '{"answers":["القانون الروماني","الطب الإغريقي","العلاقات السياسية في القرن التاسع عشر","المعاجم الاقتصادية"],"correctAnswer":"1"}',
    @created_at, @created_at
);
SET @question_id = LAST_INSERT_ID();
INSERT INTO question_quiz (quiz_id, question_id, `order`, score, created_at, updated_at)
VALUES (@quiz_id, @question_id, 1, 1, @created_at, @created_at);

INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
VALUES (
    'مراحل الأزمة',
    'ما هي المرحلة التي تبدأ فيها الأزمة بالظهور لأول مرة في شكل إحساس مبهم ينذر بخطر غير محدد المعالم؟',
    @multiple_choice_type_id,
    '{"answers":["مرحلة نمو الأزمة","مرحلة نضج الأزمة","مرحلة ميلاد الأزمة","مرحلة انحسار الأزمة"],"correctAnswer":"2"}',
    @created_at, @created_at
);
SET @question_id = LAST_INSERT_ID();
INSERT INTO question_quiz (quiz_id, question_id, `order`, score, created_at, updated_at)
VALUES (@quiz_id, @question_id, 2, 1, @created_at, @created_at);

INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
VALUES (
    'الاجتماعات',
    'أي من العبارات التالية تعتبر من "مبررات عدم عقد أي اجتماع"؟',
    @multiple_choice_type_id,
    '{"answers":["مناقشة مشكلات أو قضايا معقدة","الحاجة إلى بناء قبول عام وتعهد والتزام","عندما لا يكون المدير مستعداً","نشر معلومات تحتاج إلى اتصال مباشر مع كافة الأطراف"],"correctAnswer":"2"}',
    @created_at, @created_at
);
SET @question_id = LAST_INSERT_ID();
INSERT INTO question_quiz (quiz_id, question_id, `order`, score, created_at, updated_at)
VALUES (@quiz_id, @question_id, 3, 1, @created_at, @created_at);

INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
VALUES (
    'الفرق بين الكارثة والأزمة',
    'الكارثة أعم وأشمل من الأزمة، بينما تنحصر الأزمة في الحوادث ذات الدمار الشامل.',
    @true_false_type_id,
    '{"answers":["true","false"],"correctAnswer":"1"}',
    @created_at, @created_at
);
SET @question_id = LAST_INSERT_ID();
INSERT INTO question_quiz (quiz_id, question_id, `order`, score, created_at, updated_at)
VALUES (@quiz_id, @question_id, 4, 1, @created_at, @created_at);

INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
VALUES (
    'الاجتماعات الرسمية',
    'من القواعد الأساسية لسير الاجتماعات الرسمية، يجوز طرح موضوعات لم تذكر في جدول الأعمال.',
    @true_false_type_id,
    '{"answers":["true","false"],"correctAnswer":"1"}',
    @created_at, @created_at
);
SET @question_id = LAST_INSERT_ID();
INSERT INTO question_quiz (quiz_id, question_id, `order`, score, created_at, updated_at)
VALUES (@quiz_id, @question_id, 5, 1, @created_at, @created_at);

COMMIT;
