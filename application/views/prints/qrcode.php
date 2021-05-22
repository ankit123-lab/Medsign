<html>
    <head>
        <style>
        body{ font-family: "gotham_book"; color: #000000;}
            .free-serif-font { font-family: gotham_book; }
            .gotham_light{
                font-family: 'gotham_light';
            }
            .gotham_book{
                font-family: 'gotham_book';
            }
            .gotham_medium{
                font-family: 'gotham_medium';
            }
            .text_center{
                text-align: center;
            }
            .sub_title{
                font-family: "gotham_medium";
                font-size: 20px;
                margin: 5px 0px 0px;
                font-weight: bold;
            }
        </style>
    </head>
    <body>
        <div class="text_center" style="padding-top: 20px;">
            <p class="sub_title">Scan below QR code for patient registraton</p>  
            <p class="sub_title"><?= DOCTOR." ".  $doctor_data['user_first_name'] . " " . $doctor_data['user_last_name']; ?></p>
            <p class="sub_title"><?= $doctor_data['clinic_name']; ?></p>
            <p style="margin: 0;"><?= $clinic_address; ?></p>
        </div>
        <div style="text-align: center; margin-top: 10px;">
            <?= $qrcode_img; ?>
        </div>
    </body>
</html>
