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

        .article-list {
            width: 60%;
            margin: 0 auto;
        }

        .article-list div.manga {
            padding: 20px;
            border: 1px solid #000;
        }

        .article-list div.manga a {
            color: #000;
            text-decoration: none;
        }

        a.view-detail {
            border: 1px solid #000;
            padding: 6px;
            background: orange;
        }

    </style>

<body>
    


    <div id="wrapper">


        <div class="container">
            <h3>RSS Registration screen</h3>

            <div class="flex">
                <div>Article Search</div> 
                <form action="<?= $base_url ?>" method="GET">
                    <input type="text" name="manga">
                    <input type="submit" name="search">
                </form>
            </div>
            
            <?php if(!is_null($manga_list)) : ?>
                <div class="article-list">
                    <?php foreach($manga_list as $manga): ?>
                        <div class="manga">
                            <p>Manga Title <b><?= $manga['manga_title'] ?></b> </p>
                            <p>Manga Date <b><?= $manga['manga_date'] ?></b> </p>
                            <a href="<?= $manga_detail_url.$manga['manga_id'] ?>" class="view-detail">View Detail</a>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
            

        </div>

        

    </div>
    </body>

</html>