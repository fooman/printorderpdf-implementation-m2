<?php

namespace Fooman\PrintOrderPdf\Plugin;

/**
 * @author     Kristof Ringleff
 * @package    Fooman_PrintOrderPdf
 * @copyright  Copyright (c) 2015 Fooman Limited (http://www.fooman.co.nz)
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
class PaymentInfoBlockPlugin
{

    private $appState;

    private $resolver;

    private $design;

    public function __construct(
        \Magento\Framework\App\State $appState,
        \Magento\Framework\View\DesignInterface $design,
        \Magento\Framework\View\Element\Template\File\Resolver $resolver
    ) {
        $this->appState = $appState;
        $this->design = $design;
        $this->resolver = $resolver;
    }

    /**
     * when creating a pdf from a non-admin context the admin pdf template is not found
     * emulate the backend to retrieve it
     *
     * @param \Magento\Payment\Block\Info $subject
     * @param \Closure                    $proceed
     *
     * @return mixed
     * @throws \Exception
     */
    public function aroundToPdf(
        \Magento\Payment\Block\Info $subject,
        \Closure $proceed
    ) {
        return $this->appState->emulateAreaCode(
            'adminhtml',
            $proceed
        );
    }

    public function aroundGetTemplateFile(
        \Magento\Payment\Block\Info $subject,
        \Closure $proceed,
        $template = null
    ) {
        $params = [
            'module' => $subject->getModuleName(),
            'store_id' => $subject->getMethod()->getStore(),
            'theme' => $this->design->getDesignTheme()->getThemePath()
        ];
        $area = $subject->getArea();
        if ($area) {
            $params['area'] = $area;
        }
        return $this->resolver->getTemplateFileName($template ?: $subject->getTemplate(), $params);
    }
}
