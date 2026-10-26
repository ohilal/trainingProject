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
        WHERE title = N'اختبار خطط العمل وكتابة التقارير'
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
        N'اختبار خطط العمل وكتابة التقارير',
        N'اختبار خطط العمل وكتابة التقارير',
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
        N'ما الذي يرمز إليه الحرف "M" في نموذج الأهداف الذكية (SMART) المُستخدم لضمان أن تكون أهداف خطة العمل قابلة للتنفيذ والقياس؟',
        @multiple_choice_type_id,
        N'{"answers":["محفزة (Motivating)","قابلة للقياس (Measurable)","مرنة (Modifiable)","محددة زمنياً (Mapped)"],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 1, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #2',
        N'يُعد "الملخص التنفيذي" أول عنصر يظهر في خطة العمل، ولكنه يُكتب فعلياً في المرحلة الأخيرة بعد الانتهاء من إعداد كافة تفاصيل الخطة، لضمان شموليته ودقته.',
        @true_false_type_id,
        N'{"answers":["true","false"],"correctAnswer":"0"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 2, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #3',
        N'أي من العناصر التالية يُعد العنصر النهائي ضمن عناصر خطة العمل، ويحتوي على معلومات حول المبالغ المالية التي تمثل رأس المال والإيرادات المتوقعة خلال فترة زمنية معينة؟',
        @multiple_choice_type_id,
        N'{"answers":["الملخص التنفيذي","خطة البيع (التسويق)","خطة التشغيل (الإنتاج)","الخطة المالية"],"correctAnswer":"3"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 3, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #4',
        N'من فوائد خطة العمل أنها "تعزز القدرة على التفويض"، وذلك من خلال توضيح طبيعة المسؤوليات الخاصة بالأفراد، بحيث يمتلك كل شخص مهمة أو مسؤولية محددة خاصة به.',
        @true_false_type_id,
        N'{"answers":["true","false"],"correctAnswer":"0"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 4, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #5',
        N'ما هي المرحلة الأخيرة والمفضلة في خطوات كتابة تقرير العمل، والتي يُراعى فيها مراجعة الجمل للتأكد من سلامتها لغوياً، ومراعاة علامات الترقيم وتوافق الأزمنة؟',
        @multiple_choice_type_id,
        N'{"answers":["تحديد الهدف من مادة التقرير","مراجعة الأفكار الرئيسية وترتيبها","المراجعة والتدقيق النهائي","كتابة الهيكل البنائي للتقرير"],"correctAnswer":"2"}',
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
