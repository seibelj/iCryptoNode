<html>
<head>

    <script>
        var NODE_TYPE = "<?php echo NODE_TYPE; ?>";
        var API_ROOT = "<?php echo API_ROOT; ?>";
        var VUE_GLOBALS = {};
    </script>

    <?php /* cache busters */ ?>
    <script src="/js/jquery-3.3.1.slim.min.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/lodash.min.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/axios.min.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/vue.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/popper.min.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/bootstrap.min.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/ansi_up.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/vpn_map.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/progress_bar.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/utils.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    
    <script src="/js/components/node.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/components/wifi.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/components/vpn.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/components/logs.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    <script src="/js/components/update.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>
    
    <script src="/js/app.js?<?php echo ICRYPTONODE_VERSION; ?>"></script>

    <link rel="stylesheet" href="/css/bootstrap.min.css?<?php echo ICRYPTONODE_VERSION; ?>">
    <link rel="stylesheet" href="/css/all.min.css?<?php echo ICRYPTONODE_VERSION; ?>">
    <link rel="stylesheet" href="/css/app.css?<?php echo ICRYPTONODE_VERSION; ?>">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>iCryptoNode</title>
</head>
<body>


    <div id="app" class="container">

        <?php include "nav.php"; ?>

        <?php include "node.php"; ?>

        <?php include "wifi.php"; ?>

        <?php include "vpn.php"; ?>
        
        <?php include "logs.php"; ?>

        <?php include "update.php"; ?>

        <div style="margin-top: 20px;">
            <small>Copyright 2018 Jamesys Technologies LLC. By using this software, you agree to the iCryptoNode
            <a href="https://icryptonode.com/pages/terms-of-service-privacy-policy" target="_blank">Terms of Service</a></small>
        </div>
        
    </div>

    

</body>

</html>