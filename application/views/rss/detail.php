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
            <h3>RSS registration screen</h3>

            <?php if(is_null($manga_detail)): ?>

                <p>There is no manga related with this manga id <b><?= $manga_id ?></b></p>

            <?php else: ?>
                <div class="flex">
                    <div>Article ID</div> 
                    <div><?= $manga_detail->manga_id ?></div>
                </div>
                
                <div class="flex">
                    <p>TItle</p>
                    <p><b><?= $manga_detail->manga_title ?></b></p>
                </div>

                <div class="flex">
                    <p>Link</p>
                    <p><b><?= MANGA_URL.$manga_detail->manga_id ?></b></p>
                </div>

                <div class="flex">
                    <p>Explanation</p>
                    <p><b><?= $manga_detail->manga_intro ?></b></p>
                </div>

                <div class="flex">
                    <p>Contents</p>
                    <p><b><?= $manga_detail->manga_detail ?></b></p>
                </div>

                <div class="flex">
                    <p>Image file associated with the article</p>
                    <?php if(!is_null($manga_media)): ?>
                        <p>
                            <b><?= KOSODATE_IMG_URL.$manga_media[0]['img_url'] ?></b>
                        </p>
                    <?php endif; ?>
                </div>

                <div class="flex">
                    <p>Image file displayed in the article list</p>
                    <?php if(!is_null($manga_media)): ?>
                        <p>
                            <?php foreach($manga_media as $media): ?>
                                <b><?= KOSODATE_IMG_URL.$media['img_url'] ?></b>
                            <?php endforeach; ?>
                        </p>
                    <?php endif; ?>
                </div>

                <?php if(!is_null($related_manga)): ?>
                    <div class="flex">
                        <p>Related link 1</p>
                        <p>
                            <b><?= MANGA_URL.$related_manga[0]['manga_id'] ?></b>
                        </p>            
                    </div>

                    <div class="flex">
                        <p>Related link 2</p>
                        <p>
                            <b><?= MANGA_URL.$related_manga[1]['manga_id'] ?></b>
                        </p>            
                    </div>

                    <div class="flex">
                        <p>Related link 3</p>
                        <p>
                            <b><?= MANGA_URL.$related_manga[2]['manga_id'] ?></b>
                        </p>            
                    </div>
                <?php endif; ?>
            <?php endif; ?>

            <form action="<?= $base_url ?>" method="POST">
                <input type="hidden" name="manga_id" value="<?= $manga_id ?>">
                <input type="submit" name="manga_register" value="register">
            </form>

        </div>

        

    </div>
    </body>

</html>