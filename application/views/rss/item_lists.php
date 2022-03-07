<html>
    <style>
        .container {
            width: 60%;
            margin: 0 auto;
            border: 1px solid #000;
            padding: 20px;
        }

        h3 {
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
        }

        .flex {
            display: flex;
            width: 60%;
            margin: 0 auto;
            justify-content: space-between;
            margin-bottom: 20px;
        }

        .flex > div:first-child {width: 40%;}

        .flex > div:last-child {
            width: 60%;
        }

        .flex p {
            border: 1px solid #000;
            padding: 20px;
        }

        .flex p a {
            text-decoration: none;
            color: #000;
        }

    </style>

<body>
    


    <div id="wrapper">


        <div class="container">
            <h3>RSS posting screen</h3>

            <div class="flex">
                <div>Scheduled release date</div> 
                <div><?= $release_date ?></div>
            </div>
            
            <div class="flex">
                <div>Posted content</div>
                <div>
                    <a href="ad_modify_rss/sign_up">sign up</a>
                    <p><a href="#">newly registered contents</a></p>
                    <i><a href="ad_modify_rss/modify">Correction or deletion of posted content</a></i>
                    <p><a href="">Corrected registration content</a></p>
                    <p><a href="">Corrected registration content</a></p>
                    <p><a href="">Corrected registration content</a></p>
                    <p><a href="">Corrected registration content</a></p>
                </div>
            </div>
            

        </div>

        

    </div>
    </body>

</html>