<?php
set_time_limit(0);
ob_start();
?>
<style>
#drop-area {
  border: 2px dashed #ccc;
  border-radius: 20px;
  width: 480px;
  font-family: sans-serif;
  margin: 100px auto;
  padding: 20px;
}
#drop-area.highlight {
  border-color: purple;
}
p {
  margin-top: 0;
}
.my-form {
  margin-bottom: 10px;
}
.button {
  display: inline-block;
  padding: 10px;
  background: #ccc;
  cursor: pointer;
  border-radius: 5px;
  border: 1px solid #ccc;
}
.button:hover {
  background: #ddd;
}
#fileElem {
  display: none;
}
</style>

<div class='container'>
<div class='row'>
<div class='col-sm-offset-2 col-sm-8'>
<h2>One time Person Data Upload</h2>

<div id="drop-area">
  <form class="my-form">
    <p>To upload one time Person XLS file simply drag and drop it onto the dashed region</p>
    <input type="file" id="fileElem" multiple accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" onchange="handleFiles(this.files)">
    <label class="button" for="fileElem">or Select File here</label>
  </form>
</div>

</div>
</div>
</div>