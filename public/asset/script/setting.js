$(document).ready(function () {
  $(".sideBarli").removeClass("activeLi");
  $(".settingSideA").addClass("activeLi");

  const eye = document.querySelector(".feather-eye");
  const eyeoff = document.querySelector(".feather-eye-off");
  const passwordField = document.querySelector("input[type=password]");

  eye.addEventListener("click", () => {
    eye.style.display = "none";
    eyeoff.style.display = "block";
    passwordField.type = "text";
  });

  eyeoff.addEventListener("click", () => {
    eyeoff.style.display = "none";
    eye.style.display = "block";
    passwordField.type = "password";
  });

  const eye1 = document.querySelector(".eye1");
  const eyeoff1 = document.querySelector(".eye-off1");
  const passwordField1 = document.querySelector(
    "input#newPassword[type=password]"
  );

  eye1.addEventListener("click", () => {
    eye1.style.display = "none";
    eyeoff1.style.display = "block";
    passwordField1.type = "text";
  });

  eyeoff1.addEventListener("click", () => {
    eyeoff1.style.display = "none";
    eye1.style.display = "block";
    passwordField1.type = "password";
  });

  $("#settingsForm").on("submit", function (event) {
    event.preventDefault();

    if (user_type == 1) {
      var formdata = new FormData($("#settingsForm")[0]);
      console.log(formdata);

      $.ajax({
        url: `${domainUrl}saveSettings`,
        type: "POST",
        data: formdata,
        dataType: "json",
        contentType: false,
        cache: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            iziToast.show({
              title: "Error",
              message: "Setting not found",
              color: "red",
              position: toastPosition,
              transitionIn: transitionInAction,
              transitionOut: transitionOutAction,
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/x.svg`,
            });
          } else if (response.status == true) {
            iziToast.show({
              title: "Success",
              message: "Setting updated Successfully",
              color: "green",
              position: toastPosition,
              transitionIn: transitionInAction,
              transitionOut: transitionOutAction,
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/check-circle.svg`,
            });
            $("#reloadContent").load(location.href + " #reloadContent>*", "");
          }
        },
        error: function (err) {
          console.log(err);
        },
      });
    } else {
      iziToast.error({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: transitionInAction,
        transitionOut: transitionOutAction,
        timeout: 3000,
        animateInside: true,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });

  $(document).on("submit", "#changePasswordForm", function (e) {
    e.preventDefault();
    if (user_type == 1) {
      let formData = new FormData($("#changePasswordForm")[0]);
      $.ajax({
        type: "POST",
        url: `${domainUrl}changePassword`,
        data: formData,
        contentType: false,
        processData: false,
        success: function (response) {
          if (response.status == false) {
            console.log(response.message);
            iziToast.show({
              title: "Error",
              message: "Old Password does not match",
              color: "red",
              position: toastPosition,
              transitionIn: transitionInAction,
              transitionOut: transitionOutAction,
              timeout: 3000,
              animateInside: false,
              iconUrl: `${domainUrl}asset/img/x.svg`,
            });
          } else if (response.status == true) {
             iziToast.show({
               title: "Success",
               message: "Password change successfully",
               color: "green",
               position: toastPosition,
               transitionIn: transitionInAction,
               transitionOut: transitionOutAction,
               timeout: 3000,
               animateInside: false,
               iconUrl: `${domainUrl}asset/img/check-circle.svg`,
             });
            $("#changePasswordForm").load(location.href + " #changePasswordForm>*","" );
          }
        },
      });
    } else {
      iziToast.show({
        title: "Oops",
        message: "You are tester",
        color: "red",
        position: toastPosition,
        transitionIn: transitionInAction,
        transitionOut: transitionOutAction,
        timeout: 3000,
        animateInside: false,
        iconUrl: `${domainUrl}asset/img/x.svg`,
      });
    }
  });
});
