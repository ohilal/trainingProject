SET XACT_ABORT ON;

BEGIN TRY
    BEGIN TRANSACTION;

    DECLARE @created_at datetime2 = SYSDATETIME();
    DECLARE @quiz_id bigint;
    DECLARE @question_id bigint;
    DECLARE @multiple_choice_type_id bigint;
    DECLARE @true_false_type_id bigint;

    SELECT TOP (1) @multiple_choice_type_id = id
    FROM question_types
    WHERE title = N'TestQuestion' AND deleted_at IS NULL
    ORDER BY id;

    SELECT TOP (1) @true_false_type_id = id
    FROM question_types
    WHERE title = N'TrueFalseQuestion' AND deleted_at IS NULL
    ORDER BY id;

    IF @multiple_choice_type_id IS NULL
        THROW 50001, 'Active TestQuestion type was not found.', 1;

    IF @true_false_type_id IS NULL
        THROW 50002, 'Active TrueFalseQuestion type was not found.', 1;

    IF EXISTS (
        SELECT 1
        FROM quizzes
        WHERE title = N'اختبار بناء الشخصية والعرض والتحدث الفعال'
          AND deleted_at IS NULL
    )
        THROW 50003, 'This quiz already exists.', 1;

    DECLARE @quiz_ids TABLE (id bigint);

    INSERT INTO quizzes (
        title,
        description,
        attempt,
        duration,
        is_mentor,
        is_shuffle,
        min_pass_score,
        show_question,
        random_question,
        created_at,
        updated_at
    )
    OUTPUT inserted.id INTO @quiz_ids (id)
    VALUES (
        N'اختبار بناء الشخصية والعرض والتحدث الفعال',
        N'اختبار بناء الشخصية والعرض والتحدث الفعال',
        0,
        0,
        0,
        0,
        80,
        N'StepByStep',
        0,
        @created_at,
        @created_at
    );

    SELECT @quiz_id = id FROM @quiz_ids;

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #1',
        N'وفقاً للنص، ما هو العنصر الذي يُعتبر "القاعدة المعرفية" التي تبنى عليها كافة العناصر الأخرى في بناء الشخصية؟',
        @multiple_choice_type_id,
        N'{"answers":["العواطف","الفكر","الإرادة","الثقافة"],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 1, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #2',
        N'ينصح النص بكتابة 50 كلمة على الأقل في كل شريحة عرض تقديمي لضمان قراءة الجمهور لجميع التفاصيل الدقيقة.',
        @true_false_type_id,
        N'{"answers":["true","false"],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 2, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #3',
        N'ما هي إحدى الطرق المقترحة في النص لتحويل القلق والعصبية إلى حماس وطاقة إيجابية قبل بدء العرض التقديمي؟',
        @multiple_choice_type_id,
        N'{"answers":["حفظ النص حرفياً وتكراره","تجنب شرب الماء للحفاظ على التركيز","الاستماع لموسيقى بصوت مرتفع أو خطاب محفز","قراءة الشرائح بصمت قبل الدخول"],"correctAnswer":"2"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 3, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #4',
        N'للحفاظ على مكانة متميزة وشخصية قوية، يجب على الشخص المبالغة في التعبير عن مشاعره السلبية والإيجابية (كالفرح والغضب) ليظهر صدقه للآخرين.',
        @true_false_type_id,
        N'{"answers":["true","false"],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 4, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #5',
        N'وفقاً لـ "5 نصائح لتصبح متحدثاً جيداً"، ما هي الخطوة الأولى التي يجب على الفرد القيام بها؟',
        @multiple_choice_type_id,
        N'{"answers":["حدد هدفك من الكلام","اختر المستمع الجيد","ثقف نفسك","اهتم بلغة جسدك"],"correctAnswer":"2"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 5, 1, @created_at, @created_at);

    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    IF @@TRANCOUNT > 0
        ROLLBACK TRANSACTION;

    THROW;
END CATCH;
