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
                    <a href="<?= $manga_signup_url ?>">sign up</a>
                    <?php if(isset($_SESSION['register_manga'])): ?>
                        <?php foreach($_SESSION['register_manga'] as $manga): ?>
                            <p>
                                Manga title : <a href="<?= $manga_detail_url.'/'.$manga['guid'] ?>"><?= $manga['title'] ?></a>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <br>There is no new manga yet<br>
                    <?php endif; ?>
                    <i><a href="ad_modify_rss/modify">Correction or deletion of posted content</a></i>
                    <?php if(isset($_SESSION['update_manga'])): ?>
                        <?php foreach($_SESSION['update_manga'] as $manga): ?>
                            <p>
                                Manga title : <a href="<?= $manga_detail_url.'/'.$manga['guid'] ?>"><?= $manga['title'] ?></a>
                            </p>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <br>There is no updated manga yet<br>
                    <?php endif; ?>
                </div>
            </div>

            <div class="flex">
                <form action="<?= $manga_execute_url ?>" method="POST">
                    <input type="submit" value="Run Execution" name='execute_manga'>
                </form>
            </div>
            

        </div>

        

    </div>
    </body>

</html>