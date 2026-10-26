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
        WHERE title = N'اختبار التفويض والولاء المؤسسي'
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
        N'اختبار التفويض والولاء المؤسسي',
        N'اختبار التفويض والولاء المؤسسي',
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
        N'وفقاً للنص، ما هي الأمور التي لا يجوز للمدير تفويضها ويجب أن يحتفظ بها؟',
        @multiple_choice_type_id,
        N'{"answers":["القرارات الروتينية اليومية.","مهام الإثراء التي تعطي معنى للعمل.","المهام الإشرافية، والأمور الشخصية، والطوارئ، والأمور السرية والحساسة.","المهام التي تهدف إلى تدريب المرؤوسين وتطوير مهاراتهم."],"correctAnswer":"2"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 1, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #2',
        N'مبدأ "المسؤولية المطلقة" يعني أنه بمجرد تفويض السلطة للمرؤوس، يتخلى المدير تماماً عن مسؤوليته النهائية تجاه النتائج ولا يمكن محاسبته على أخطاء مرءوسيه.',
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
        N'ما هو الفرق الجوهري بين "الولاء المؤسسي" و"الرضا عن العمل" وفقاً لما ورد في النص؟',
        @multiple_choice_type_id,
        N'{"answers":["لا يوجد فرق، فالمصطلحان يعبران عن نفس المفهوم تماماً.","قد يكون الموظف راضياً عن طبيعة عمله لكنه يكره المنظمة ويرغب في الانتقال لمنظمة أخرى، بينما الولاء يعني الانتماء والإخلاص للمنظمة ذاتها والتضحية من أجلها.","الرضا عن العمل أهم بكثير من الولاء المؤسسي في ضمان استمرارية المؤسسة ونجاحها.","الولاء المؤسسي يعتمد فقط على الراتب، بينما الرضا يعتمد على العلاقات الاجتماعية."],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 3, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #4',
        N'يعتمد بناء الولاء الوظيفي حصراً على رفع الراتب المباشر (التعويض المالي)، ولا أهمية للتعويضات غير المباشرة (مثل التأمين الصحي، برامج الإسكان، أو دعم التعليم) أو لبيئة العمل الإنسانية.',
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
        N'وفقاً لـ "المدخل المشترك" (Meyer & Allen) للولاء المؤسسي المذكور في النص، ما هي الأبعاد الثلاثة التي يتكون منها الولاء؟',
        @multiple_choice_type_id,
        N'{"answers":["الانتماء المالي، الانتماء الاجتماعي، والانتماء الإداري.","الانتماء الاستمراري، الانتماء العاطفي، والانتماء المعياري.","الانتماء الشخصي، الانتماء المهني، والانتماء التنظيمي.","الانتماء القسري، الانتماء الطوعي، والانتماء المؤقت."],"correctAnswer":"1"}',
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
