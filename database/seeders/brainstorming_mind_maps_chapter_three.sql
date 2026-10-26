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
        WHERE title = N'اختبار الفصل الثالث: العصف الذهني والخرائط الذهنية'
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
        N'اختبار الفصل الثالث: العصف الذهني والخرائط الذهنية',
        N'اختبار الفصل الثالث: العصف الذهني والخرائط الذهنية',
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
        N'من هو الشخص الذي يُنسب إليه ابتكار أسلوب "العصف الذهني" في الثلاثينيات من القرن الماضي في وكالة إعلانات بنيويورك؟',
        @multiple_choice_type_id,
        N'{"answers":["توني بوزان","أليكس أسبورن","كيفن لينش","سورين ماتي"],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 1, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #2',
        N'من المبادئ الأساسية لنجاح جلسات العصف الذهني تقييم ونقد الأفكار فور طرحها لضمان عدم تسجيل أفكار غير عملية.',
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
        N'وفقاً للنص، ما هو العدد الأقصى المفضل للمشاركين في جلسة العصف الذهني لضمان إتاحة الفرصة للجميع للمشاركة بفعالية دون فقدان الحماس؟',
        @multiple_choice_type_id,
        N'{"answers":["50 شخصاً","20 شخصاً","100 شخص","لا يوجد حد أقصى"],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 3, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #4',
        N'يعتمد أحد المبادئ الأساسية للعصف الذهني على "الكم قبل الكيف"، حيث إن توليد أكبر قدر ممكن من الأفكار يزيد من احتمالية ظهور أفكار مبتكرة وجيدة في النهاية.',
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
        N'أي من العوائق التالية يُصنف ضمن "العوائق النفسية" للتفكير الإبداعي حسب النص؟',
        @multiple_choice_type_id,
        N'{"answers":["التسليم الأعمى للافتراضات","الخوف من الفشل وعدم الثقة بالنفس","التقيد بأنماط محددة للتفكير","التسرع في تقييم الأفكار"],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 5, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #6',
        N'الخريطة الذهنية هي تقنية بيانية وضعها توني بوزان، وتعتمد على ربط الأفكار باستخدام الخطوط أو المنحنيات مع الفكرة الرئيسية لتشكل شبكة من الروابط والعلاقات.',
        @true_false_type_id,
        N'{"answers":["true","false"],"correctAnswer":"0"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 6, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #7',
        N'ما هي المرحلة الأخيرة في خطوات جلسة العصف الذهني، والتي يتم فيها تصنيف الأفكار إلى (أفكار أصيلة وقابلة للتطبيق، أفكار تحتاج لمزيد من البحث، أفكار مستثناة)؟',
        @multiple_choice_type_id,
        N'{"answers":["إعادة صياغة الموضوع","تهيئة جو الإبداع","جلسة التقييم","تحديد أغرب فكرة"],"correctAnswer":"2"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 7, 1, @created_at, @created_at);

    COMMIT TRANSACTION;
END TRY
BEGIN CATCH
    IF @@TRANCOUNT > 0
        ROLLBACK TRANSACTION;

    THROW;
END CATCH;
