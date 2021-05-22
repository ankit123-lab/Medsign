<html>
    <head>
        <style>
            body{
                font-family: "gotham_book";
            }
            .gotham_light{
                font-family: 'gotham_light';
            }
            .gotham_book{
                font-family: 'gotham_book';
            }
            .gotham_medium{
                font-family: 'gotham_medium';
            }
            .title{
                font-family: "gotham_medium";
                font-size: 24px;
            }
            .sub_title{
                font-family: "gotham_medium";
                font-size: 20px;
                margin: 25px 0px 0px;
            }
            .text_center{
                text-align: center;
            }
            .text_right{
                text-align: right;
            }
            .text_left{
                text-align: left;
            }
            .pull_left{
                float: left;    
            }
            .pull_right{
                float: right;
            }
            .width_100{
                width: 100%;
            }
            .width_50{
                width: 50%;
            }
            .width_60{
                width: 60%;
            }
            .width_70{
                width: 70%;
            }
            .width_20{
                width: 20%;
            }
            .width_30{
                width: 30%;
            }
            .width_40{
                width: 40%;
            }
            .border_top_bottom{
                border-bottom: 1px solid #d1d1d1;
            }
            .border_bottom{
                border-bottom: 1px solid #d1d1d1;
            }
            .p_15_top_bottom{
                padding: 5px 0px;
            }
            .p_15_left_right{
                padding: 0xp 15px;
            }
            .patient_info p{
                margin: 0px;
            }
            .custom_table tr td {
                border-bottom:1pt solid #d1d1d1;
                padding: 10px 0px;
            }
            .custom_table { 
                border-collapse: collapse; 
                width: 100%;
                margin-top: 10px;

            }
            .custom_td_width .heading td {
                width: 20%;
                word-break: break-all;
                word-wrap: break-word;
                hyphens: auto;
            }
            .text_upper{
                text-transform: uppercase;
            }
            .font_24{
                font-size: 24px;
            }
            .color_green{
                color: green;
            }
            .no_margin{
                margin: 0px;
            }
            .background_heading{
                background: #d1d1d1;
            }
            .background_heading td{
                font-weight: bold;
            }
            .clearfix{
                clear: both;
            }
            .bill_container p{
                margin: 5px;
            }
            .bill_container{
                margin-top: 20px;                
            }
        </style>
    </head>
    <body>
        <?= $invoice_body; ?>
    </body>
</html>