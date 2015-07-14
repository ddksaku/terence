
<div id="successLogin"></div>
<div class="text_success"><img src="images/loader/loader_green.gif" alt="Synergy Admin" /><span>Please wait</span></div>

<div id="login">
  <div class="ribbonreset"></div>
  <div class="inner clearfix">
  <div class="logo"><img src="images/logo/logo_login.png" alt="Synergy Admin" /></div>
  <div class="formLogin">
    <form name="formnewpass" id="formnewpass" method="post">

        <div class="tip">
              <input name="password" type="password" id="password" title="New Password" />
        </div>
        <div class="tip">
              <input name="password2" type="password" id="password2" title="Confirm New Password" />
        </div>

        <div class="loginButton">

          <div class="pull-right" style="margin-right:-8px;">
          <input type="hidden" name="code" id="code" value="<?php echo $reset_code; ?>" />
              <div class="btn-group">
                <button type="button" class="btn btn-success white" id="save">Submit</button>
              </div>

          </div>
          <div class="clear"></div>
        </div>

  </form>
     </div>
</div>
  <div class="shadow"></div>
</div>