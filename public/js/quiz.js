$(document).ready(function () {
    $("#btnQuestions .btnQuestion").removeClass("process-step-active");
    if (style == "StepByStep") {
        StepByStep();
    } else {
        OnePage();
    }
    $("#btnQuestions .btnQuestion:first").addClass("process-step-active");

    $("#saveButton").click(function (event) {
        event.preventDefault();
        saveOnly();
    });

    $(".workout_questions").on("submit", function (event) {
        event.preventDefault();

        var form = this;
        var submitButton = $(form).find(":submit");
        submitButton.prop("disabled", true);

        saveQuestionForm(form)
            .done(function () {
                advanceToNextQuestion(form);
            })
            .fail(showSaveError)
            .always(function () {
                submitButton.prop("disabled", false);
            });
    });

    $("#saveAndClose").click(function (event) {
        event.preventDefault();

        $("#dialog-confirm").dialog({
            resizable: false,
            height: "auto",
            width: 400,
            modal: true,
            
            buttons: {
                "Save And Close": function () {
                    $(this).dialog("close");
                    saveAndClose();
                },
                Cancel: function () {
                    $(this).dialog("close");
                },
            },
        });
    });
});

function OnePage() {
    $("#questions").show();
    $(".question").show();
}

function StepByStep() {
    $("#questions").show();
    $("#questions .question").hide();
    $("#questions .question:first").show();
}

function showQuestion(question) {
    $("#btnQuestions .btnQuestion").removeClass("process-step-active");
    $("#btnQuestion-" + question).addClass("process-step-active");

    if (style == "StepByStep") {
        $("#questions .question").hide();
        $("#question-" + question).show();
    } else {
        scrollToAnchor(question);
    }
}

function scrollToAnchor(question) {
    var aTag = $("#question-" + question);
    $("html,body").animate({ scrollTop: aTag.offset().top }, "slow");
}

function saveAndClose() {
    saveForms($(".workout_questions").toArray())
        .done(function () {
            window.location.href = $("#saveAndClose").data("next-url");
        })
        .fail(showSaveError);
}

function saveOnly() {
    var forms = style == "StepByStep"
        ? $("#questions .question:visible .workout_questions").toArray()
        : $(".workout_questions").toArray();

    saveForms(forms)
        .done(function () {
            if (style == "StepByStep" && forms.length > 0) {
                advanceToNextQuestion(forms[0]);
            }

            alert(quizSavedMessage);
        })
        .fail(showSaveError);
}

function saveForms(forms) {
    var sequence = $.Deferred().resolve();

    forms.forEach(function (form) {
        sequence = sequence.then(function () {
            return saveQuestionForm(form);
        });
    });

    return sequence;
}

function saveQuestionForm(form) {
    return $.ajax({
        url: $(form).attr("action"),
        method: $(form).attr("method") || "POST",
        data: new FormData(form),
        processData: false,
        contentType: false
    });
}

function advanceToNextQuestion(form) {
    if (style != "StepByStep") {
        return;
    }

    var questions = $("#questions .question");
    var currentIndex = questions.index($(form).closest(".question"));

    if (currentIndex >= 0 && currentIndex + 1 < questions.length) {
        var nextQuestionId = questions.eq(currentIndex + 1).attr("id").replace("question-", "");
        showQuestion(nextQuestionId);
    }
}

function showSaveError(xhr) {
    var message = xhr.responseJSON && xhr.responseJSON.message
        ? xhr.responseJSON.message
        : quizSaveErrorMessage;

    alert(message);
}