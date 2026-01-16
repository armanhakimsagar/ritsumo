<?php
    $direction = 'ltr';
    $locale = app()->getLocale();
    
    // Check for RTL languages - Arabic and Hebrew always use RTL
    if (in_array($locale, ['ar', 'he'])) {
        $direction = 'rtl';
    } else {
        // For non-RTL languages, check user layout setting
        if (auth()->check()) {
            $userDirection = getSetting('layoutDirection', 'left');
            if ($userDirection === 'right') {
                $direction = 'rtl';
            } elseif ($userDirection === 'left') {
                $direction = 'ltr';
            }
        }
    }
?>
<!DOCTYPE html>
<html lang="<?php echo e(str_replace('_', '-', app()->getLocale())); ?>" dir="<?php echo e($direction); ?>" class="<?php echo \Illuminate\Support\Arr::toCssClasses(['dark' => ($appearance ?? 'system') == 'dark']); ?>">
    <head>
        <base href="<?php echo e(\Illuminate\Support\Facades\Request::getBasePath()); ?>">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

        
        <script>
            (function() {
                const appearance = '<?php echo e($appearance ?? "system"); ?>';

                if (appearance === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;

                    if (prefersDark) {
                        document.documentElement.classList.add('dark');
                    }
                }
            })();
        </script>

        
        <style>
            html {
                background-color: oklch(1 0 0);
            }

            html.dark {
                background-color: oklch(0.145 0 0);
            }
        </style>

        <title inertia><?php echo e(config('app.name', 'Laravel')); ?></title>
        
        
        <?php
            $seoSettings = settings();
        ?>
        <!-- Debug: <?php echo e(json_encode($seoSettings)); ?> -->
        <?php if(!empty($seoSettings['metaKeywords'])): ?>
            <meta name="keywords" content="<?php echo e($seoSettings['metaKeywords']); ?>">
        <?php endif; ?>
        <?php if(!empty($seoSettings['metaDescription'])): ?>
            <meta name="description" content="<?php echo e($seoSettings['metaDescription']); ?>">
        <?php endif; ?>
        <?php if(!empty($seoSettings['metaImage'])): ?>
            <meta property="og:image" content="<?php echo e(str_starts_with($seoSettings['metaImage'], 'http') ? $seoSettings['metaImage'] : url($seoSettings['metaImage'])); ?>">
        <?php endif; ?>
        <meta property="og:title" content="<?php echo e(config('app.name', 'Laravel')); ?>">
        <meta property="og:type" content="website">
        <meta name="twitter:card" content="summary_large_image">

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />
        <script src="<?php echo e(asset('js/jquery.min.js')); ?>"></script>
        <?php echo app('Tighten\Ziggy\BladeRouteGenerator')->generate(); ?>
        <?php if(app()->environment('local') && file_exists(public_path('hot'))): ?>
            <?php echo app('Illuminate\Foundation\Vite')->reactRefresh(); ?>
        <?php endif; ?>
        <?php echo app('Illuminate\Foundation\Vite')(['resources/js/app.tsx']); ?>
        <script>
            window.baseUrl = '<?php echo e(url('/')); ?>';

            // Set initial locale for i18next
            fetch('<?php echo e(route('initial-locale')); ?>')
                .then(response => response.text())
                .then(locale => {
                    window.initialLocale = locale;
                })
                .catch(() => {
                    window.initialLocale = 'en';
                });
            
            // Apply global sidebar and layout settings
            window.addEventListener('DOMContentLoaded', function() {
                <?php if(config('app.is_demo')): ?>
                    // Demo mode: Get settings from cookies
                    function getCookie(name) {
                        const value = `; ${document.cookie}`;
                        const parts = value.split(`; ${name}=`);
                        if (parts.length === 2) return decodeURIComponent(parts.pop().split(';').shift());
                        return null;
                    }
                    
                    const brandCookie = getCookie('brandSettings');
                    let globalSettings = {};
                    
                    if (brandCookie) {
                        try {
                            globalSettings = JSON.parse(brandCookie);
                        } catch (e) {
                            console.error('Failed to parse brand settings cookie');
                        }
                    }
                <?php else: ?>
                    // Normal mode: Get settings from database
                    <?php
                        $user = auth()->user();
                        $currentSettings = [];
                        if ($user && $user->current_workspace_id && isSaasMode()) {
                            $workspace = $user->currentWorkspace;
                            if ($workspace && $workspace->owner_id) {
                                $currentSettings = settings($workspace->owner_id, $user->current_workspace_id);
                            } else {
                                $currentSettings = settings($user->id, $user->current_workspace_id);
                            }
                        } else {
                            $currentSettings = settings();
                        }
                    ?>
                    
                    const globalSettings = <?php echo json_encode($currentSettings, 15, 512) ?>;
                <?php endif; ?>
                
                if (globalSettings.sidebarVariant || globalSettings.sidebarStyle) {
                    const sidebarSettings = {
                        variant: globalSettings.sidebarVariant || 'inset',
                        style: globalSettings.sidebarStyle || 'plain',
                        collapsible: JSON.parse(localStorage.getItem('sidebarSettings') || '{}').collapsible || 'icon'
                    };
                    localStorage.setItem('sidebarSettings', JSON.stringify(sidebarSettings));
                }
                
                if (globalSettings.layoutDirection) {
                    localStorage.setItem('layoutPosition', globalSettings.layoutDirection);
                }
            });
        </script>
        <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->head; } ?>
    </head>
    <body class="font-sans antialiased">
        <?php if (!isset($__inertiaSsrDispatched)) { $__inertiaSsrDispatched = true; $__inertiaSsrResponse = app(\Inertia\Ssr\Gateway::class)->dispatch($page); }  if ($__inertiaSsrResponse) { echo $__inertiaSsrResponse->body; } else { ?><div id="app" data-page="<?php echo e(json_encode($page)); ?>"></div><?php } ?>
    </body>
</html><?php /**PATH D:\xampp\htdocs\ritsumo\resources\views/app.blade.php ENDPATH**/ ?>