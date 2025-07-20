<?php
// register-front.php

session_start();

// In this section, we are handling the response from the backend.  
// We have two main scenarios:
// 1.The successful one that returns with message ($message) and message type ($mtype) = 'success'.
// 2. The failed one, that returns with message ($message) and message type ($mtype) = 'danger', 
// along with the given data ($old) and the error messages ($errors). 
// The types 'danger' and 'success' are following Bootstrap's color convention.
$message = $_SESSION['message'] ?? ''; unset($_SESSION['message']);
$mtype   = $_SESSION['mtype']   ?? ''; unset($_SESSION['mtype']);
$errors  = $_SESSION['errors']  ?? []; unset($_SESSION['errors']);
$old     = $_SESSION['old']     ?? []; unset($_SESSION['old']);

$phone_codes = [ 1 => '+30', 2 => '+357', 3 => '+44', ];
if( !empty($old) ) { $phone_code = $phone_codes[$old['country']]; } else { $phone_code = '+00'; }

// Generating CSRF token
$_SESSION['csrf_token'] = bin2hex(random_bytes(32)); 
$csrf_token = $_SESSION['csrf_token'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="./style.css">
    
    <title> User Registration </title>
</head>
<body>

<div class="header">
    <div class="container">
        <div class="row">
            <div class="col-auto">
                <img src="./HFM.png" alt="HFM" class="logo">
            </div>
        </div>
    </div>
</div>

<div class="main">
<div class="container form-frame">
<div class="row justify-content-center my-5 row-custom">

    <h2 class="text-center my-5 display-6 fw-bold text-dark"> 
        <span class="text-warning"> HF Markets Group </span> <br> <span class="text-light"> Become a member </span> 
    </h2>

    <div class="col-auto registration-wrapper">

        <h3 class="text-center mb-4"> Registration Form </h3>

        <?php /* Simple response message. Green on success, red on failure. */ ?>
        <?php if ($message): ?>
            <div class="alert alert-<?php echo $mtype; ?> alert-dismissible fade show" role="alert">
                <?php echo $message; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>
    
        <form action="register-back.php" method="POST" class="needs-validation" novalidate>
            
            <input type="hidden" name="csrf_token" value="<?php echo $csrf_token; ?>">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <?php /* I case of error is rendered with the 'is-invalid' class of Bootstrap, prepopulated with the old value, and the list of errors */ ?>
                    <input type="text" class="form-control <?php echo isset($errors['firstname']) ? 'is-invalid' : ''; ?>"
                        id="firstname" name="firstname" value="<?php echo $old['firstname'] ?? ''; ?>" placeholder="First Name" maxlength="255" required>
                    <?php if (isset($errors['firstname'])): ?>
                        <?php foreach ($errors['firstname'] as $error): ?>
                            <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="col-md-6 mb-3">
                    <input type="text" class="form-control <?php echo isset($errors['lastname']) ? 'is-invalid' : ''; ?>"
                            id="lastname" name="lastname" value="<?php echo $old['lastname'] ?? ''; ?>" placeholder="Last Name"  maxlength="255" required>
                    <?php if (isset($errors['lastname'])): ?>
                        <?php foreach ($errors['lastname'] as $error): ?>
                            <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row">
                <div class="col-md-6 mb-3">
                    <select name="country" id="country" class="form-select <?php echo isset($errors['country']) ? 'is-invalid' : ''; ?>"  onchange="countryChange()" required>
                        <option value="" class="text-secondary" <?php echo (isset($old['country']) && $old['country'] == '') ? 'selected' : ''; ?>> Country </option>
                        <option value="1" <?php echo (isset($old['country']) && $old['country'] == 1) ? 'selected' : ''; ?>> Greece </option>
                        <option value="2" <?php echo (isset($old['country']) && $old['country'] == 2) ? 'selected' : ''; ?>> Cyprus </option>
                        <option value="3" <?php echo (isset($old['country']) && $old['country'] == 3) ? 'selected' : ''; ?>> United Kingdom </option>
                    </select>
                    <?php if (isset($errors['country'])): ?>
                        <?php foreach ($errors['country'] as $error): ?>
                            <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>

                <div class="col-md-6 mb-3">
                    <div class="input-group">
                        <label class="input-group-text"> <span id="phone_code_l" class="text-secondary"> <?php  echo $phone_code; ?> </span> </label>
                        <input type="text" class="form-control <?php echo isset($errors['phone']) ? 'is-invalid' : ''; ?>"
                            id="phone" name="phone" value="<?php echo $old['phone'] ?? ''; ?>" placeholder="Phone" pattern="[0-9]+" required>
                    </div>
                    <input type="hidden"  id="phone_code" name="phone_code" value="">
                    <?php if (isset($errors['phone'])): ?>
                        <?php foreach ($errors['phone'] as $error): ?>
                            <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <?php if (isset($errors['phone_code'])): ?>
                        <?php foreach ($errors['phone_code'] as $error): ?>
                            <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="row mb-4">
                <div class="col-md-6">
                    <input type="email" class="form-control <?php echo isset($errors['email']) ? 'is-invalid' : ''; ?>"
                        id="email" name="email" required autocomplete="email" aria-describedby="email-fb"
                        value="<?php echo $old['email'] ?? ''; ?>" placeholder="Email">
                    <?php if (isset($errors['email'])): ?>
                        <?php foreach ($errors['email'] as $error): ?>
                            <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                    <div id="email-fb" class="invalid-feedback"> Invalid Email </div>
                </div>
                <div class="col-md-6">
                    <select name="experience" id="experience" class="form-select <?php echo isset($errors['experience']) ? 'is-invalid' : ''; ?>" required>
                        <option value="" class="text-secondary" <?php echo (isset($old['experience']) && $old['experience'] == '') ? 'selected' : ''; ?>> Experience </option>
                        <option value="1" <?php echo (isset($old['experience']) && $old['experience'] == '1') ? 'selected' : ''; ?>> Entry </option>
                        <option value="2" <?php echo (isset($old['experience']) && $old['experience'] == '2') ? 'selected' : ''; ?>> Mid-Level </option>
                        <option value="3" <?php echo (isset($old['experience']) && $old['experience'] == '3') ? 'selected' : ''; ?>> Proficient </option>
                    </select>
                    <?php if (isset($errors['experience'])): ?>
                        <?php foreach ($errors['experience'] as $error): ?>
                            <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
            </div>

            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input <?php echo isset($errors['terms']) ? 'is-invalid' : ''; ?>" name="terms" required>
                <label class="form-check-label" style="font-size: 14px;"> 
                    I have read and accepted the <a href="/privacy-police" class="text-primary"> Privacy Policy </a> 
                    and <a href="/terms" class="text-primary"> Terms and Conditions </a> 
                </label>
                <?php if (isset($errors['terms'])): ?>
                    <?php foreach ($errors['terms'] as $error): ?>
                        <div class="invalid-feedback d-block"><?php echo $error; ?></div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
            
            <div class="d-flex flex-warp justify-content-center mb-3">
                <div class="mt-5">
                    <button type="submit" class="btn btn-lg btn-success register-btn"> JOIN NOW </button>
                </div>
            </div>
        </form>
    </div>

</div>
</div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>

    /* Default handling of form errors working with Bootstrap */
    (function () {
        'use strict'

        var forms = document.querySelectorAll('.needs-validation')

        Array.prototype.slice.call(forms).forEach(function (form) {
            form.addEventListener('submit', function (event) {
                if (!form.checkValidity()) { event.preventDefault(); event.stopPropagation(); }
                form.classList.add('was-validated');
            }, false);
        });
    })()

    /* When the user selects a country the phone (country) code aumatically changes. The display and the hidden input */
    var phone_codes = { 1:'+30', 2:'+357', 3:'+44',  4:'+47', };
    function countryChange() {
        let country_elem = document.getElementById('country');
        let country = country_elem.value;
        let code_elem = document.getElementById('phone_code');
        let label_elem = document.getElementById('phone_code_l');
        code_elem.value = country;
        label_elem.innerHTML = phone_codes[country] || '+00';
    }
</script>

</body>
</html>
