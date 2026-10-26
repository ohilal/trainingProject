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
        WHERE title = N'اختبار التسويق والبيع والتسويق الرقمي'
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
        N'اختبار التسويق والبيع والتسويق الرقمي',
        N'اختبار التسويق والبيع والتسويق الرقمي',
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
        N'وفقاً للنص، ما هو الفرق الجوهري بين نشاط التسويق ونشاط البيع من حيث التوقيت والتركيز؟',
        @multiple_choice_type_id,
        N'{"answers":["يبدأ نشاط البيع قبل الإنتاج، بينما يبدأ التسويق بعد الإنتاج.","يبدأ نشاط التسويق قبل الإنتاج ويركز على احتياجات المستهلك، بينما يركز البيع على السلعة بعد الإنتاج.","كلا النشاطين يعتمدان بشكل أساسي على البيئة الداخلية للشركة فقط.","لا يوجد فرق جوهري، فكلاهما يعني إقناع العميل بالشراء في اللحظة ذاتها."],"correctAnswer":"1"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 1, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #2',
        N'يقتصر التسويق الرقمي فقط على استخدام مواقع التواصل الاجتماعي، ولا يشمل قنوات أخرى مثل البريد الإلكتروني، أو محركات البحث، أو المواقع الإلكترونية.',
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
        N'في استراتيجية "SOSTAC" المعتمدة لبناء خطط التسويق الرقمي، ماذا يرمز الحرف الأول "S"؟',
        @multiple_choice_type_id,
        N'{"answers":["Strategy (الاستراتيجية)","Sales (المبيعات)","Situation Analysis (تحليل الأوضاع)","Social Media (وسائل التواصل الاجتماعي)"],"correctAnswer":"2"}',
        @created_at,
        @created_at
    );
    SET @question_id = CONVERT(bigint, SCOPE_IDENTITY());
    INSERT INTO question_quiz (quiz_id, question_id, [order], score, created_at, updated_at)
    VALUES (@quiz_id, @question_id, 3, 1, @created_at, @created_at);

    INSERT INTO questions (title, question_body, question_type_id, answer, created_at, updated_at)
    VALUES (
        N'Question #4',
        N'من أبرز مزايا التسويق الإلكتروني مقارنة بالتسويق التقليدي أنه أقل كلفة، ويتيح إمكانية الوصول إلى العملاء في أماكن بعيدة جغرافياً، ويسهل قياس وتحليل بيانات الحملات الإعلانية.',
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
        N'وفقاً لمقارنة مفاهيم البيع والتسويق، ما هو الهدف الرئيسي أو التركيز الأساسي لـ "نشاط البيع"؟',
        @multiple_choice_type_id,
        N'{"answers":["اكتشاف رغبات وطلبات الزبائن وتحليل رضا العملاء.","التركيز على السلعة والمنتج نفسه وإقناع العميل بشراء المنتج كمرحلة أخيرة.","الترويج والتسعير والإعلان وبناء صورة العلامة التجارية.","التخطيط الاستراتيجي طويل المدى الذي يبدأ قبل عملية الإنتاج."],"correctAnswer":"1"}',
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
