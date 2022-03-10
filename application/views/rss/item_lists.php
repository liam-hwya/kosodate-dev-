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
                    <a href="sign_up">sign up</a>
                    <!-- <?php var_dump($_SESSION['new_register']); ?> -->
                    <?php if(!is_null($newly_registered_items)): ?>
                        <?php foreach($newly_registered_items as $key => $item): ?>
                            <?php if(in_array($item['guid'],$_SESSION['new_register'])): ?>
                                <p>
                                    Manga title : <a href="<?= $manga_detail_url.'/'.$item['guid'] ?>"><?= $item['title'] ?></a>
                                </p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <br><i>There is no newly registered manga yet.</i><br>
                    <?php endif; ?>

                    <i><a href="ad_modify_rss/modify">Correction or deletion of posted content</a></i>
                    <!-- <?php var_dump($_SESSION['new_update']); ?> -->
                    <?php if(!is_null($newly_registered_items)): ?>
                        <?php foreach($newly_registered_items as $key => $item): ?>
                            <?php if(in_array($item['guid'],$_SESSION['new_update'])): ?>
                                <p>
                                    Manga title : <a href="<?= $manga_detail_url.'/'.$item['guid'] ?>"><?= $item['title'] ?></a>
                                </p>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <br><i>There is no newly registered manga yet.</i><br>
                    <?php endif; ?>
                </div>
            </div>
            

        </div>

        

    </div>
    </body>

</html>