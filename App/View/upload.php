<!DOCTYPE html>
<html lang="en">

  <head>
    <title>
      PHP File Uploads
    </title>
    <meta charset="UTF-8">
  </head>

  <body>

    <h1>
      File Uploads
    </h1>

    <form method="POST" enctype="multipart/form-data" action="upload">

      <input type="hidden" name="MAX_FILE_SIZE" value="<?= MAX_FILE_SIZE ?>">
      <input type="hidden" name="token" value="<?= $_SESSION['csrf_token'] ?? '' ?>">

      <label for="file">
        File
      </label>
      <input type="file" id="file" name="file">

      <button>
        Upload
      </button>

    </form>

  </body>

</html>

