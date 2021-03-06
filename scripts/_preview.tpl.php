<style>
    <?php
        foreach ($keyframes as $k_v) {
            echo implode('', $k_v);
        }
    ?>
    <?php
        foreach ($animations as $name => $group) {
            foreach ($group as $g_k => $g_v) {
                echo ".preview-{$name}-{$g_k}{$g_v}";
            }
        }
    ?>
</style>

<?php
    $background_colors = array('#FF6666', '#66FF66', '#9999FF');
    $animation_colors = array('#666600', '#006666', '#660066');
    $count = 0;
?>
<?php foreach ($animations as $name => $group) { ?>
    <?php foreach ($group as $g_k => $g_v) { ?>
        <?php $_class_name = 'preview-' . $name . '-' . $g_k; ?>
        <div class="preview-animation-item" style="background-color:<?php echo $background_colors[$count % count($background_colors)]; ?>;">
            <div class="preview-animation-area">
                <div class="preview-animation-element <?php echo $_class_name; ?> <?php echo (strpos($g_v,'infinite')?'':'not-infinite'); ?>" style="color:<?php echo $animation_colors[$count % count($animation_colors)]; ?>;"><?php echo $name; ?></div>
            </div>
            <textarea class="preview-animation-code">.<?php echo $_class_name; ?><?php echo $g_v; ?>
<?php echo implode('', $keyframes[$name]); ?></textarea>
        </div>
        <?php ++ $count; ?>
    <?php } ?>
<?php } ?>

<?php empty($animations) && print('<div style="width:100%;text-align:center;color:red;padding:5%% 0;">没抓取到任何动效</div>'); ?>