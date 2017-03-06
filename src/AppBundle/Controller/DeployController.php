<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Model\DeployPayload;
use SensioLabs\AnsiConverter\AnsiToHtmlConverter;
use SensioLabs\AnsiConverter\Theme\SolarizedTheme;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

class DeployController extends Controller implements SecureControllerInterface
{
    private $converter;

    /**
     * @Route("/payload")
     * @Method("POST")
     * @Security("has_role('ROLE_DEPLOY')")
     */
    public function postAction(DeployPayload $deployPayload, Request $request)
    {

//        dd($deployPayload);
//        dd($this->getUser());

        $response = 'controller ';
//        dd($request, $githubPayload);
        if ('pre' == $deployPayload->getBranch()) {
            $response .= 'push ' . $deployPayload->getBranch() . ' ';
            $response .= date('d/m/y H:i:s') . ' ';
//            return new Response($response);

//            $this->log(array($request->headers->all(), $githubPayload));

            exec('cd /srv/nzlab.es/pre && /usr/bin/git pull origin pre 2>&1', $output);
            $this->log("goto pre and do git pull");
            $this->log($this->ansi2Html($output));

            exec('cd /srv/nzlab.es/pre && sh ./deploy/deploy.sh dev', $output);
            $this->log("goto pre and deploy dev");
            $this->log($this->ansi2Html($output));

        }

        return new Response($response);
    }

    /**
     * @Route("/test")
     */
    public function testAction(Request $request)
    {

//        $path = '/etc';

        $path = $this->getParameter('kernel.root_dir') . '/..';
        $deploy_script = 'tools/deploy.sh';
        $env = 'dev';
//        $command = "cd $path && ls -la";
//        $command = "cd $path && sh deploy/deploy.sh dev 2>&1";

        $composer_home = $this->getParameter('composer_home');
        if (!is_dir($composer_home)) {
            throw $this->createNotFoundException(sprintf('Composer home does not exist: "%s', $composer_home));
        }
        putenv("COMPOSER_HOME=$composer_home");

//        $command = "cd $path && sh deploy/deploy.sh dev 2>&1";
//        exec('cd /srv/nzlab.es/pre && /usr/bin/git pull origin pre 2>&1', $output);
//        $command = "cd $path && /usr/bin/git pull origin master 2>&1";
//        $command = "cd $path && ls -la";

        $path = '/media/tino/data/sites/www/nzlab.es';
        $path = '/media';
        $command = "ls -la";

        chdir($path);
        $lines = $this->runCommand($command);

        $result = $this->renderConsole($lines);
        $lines = $this->runCommand($command);

        $lines = $this->runCommand('whoami');
        $result = $this->renderConsole($lines);

        $result .= $this->renderConsole($lines);
        return new Response($result);
//        echo($html);
//        exec('cd /srv/nzlab.es/pre && /usr/bin/git pull origin pre 2>&1', $output);
//        $this->log("goto pre and do git pull");
//        $this->log($output);

    }

    /**
     * Route("/")
     */
    public function defaultAction(Request $request)
    {

        return $this->render('AppBundle:DeployController:main.html.twig', array(// ...
        ));
    }

    private function ansi2Html($lines)
    {
        if (!$this->converter) {
//            $theme = new SolarizedTheme();
            $theme = null;
            $this->converter = new AnsiToHtmlConverter($theme);
        }

        if (!is_array($lines)) {
            $lines = array($lines);
        }

        $html = '<pre>';
        foreach ($lines as $line) {
            $html .= $this->converter->convert($line) . "\n";
//            $html .= $this->converter->convert($line) . "<br>\r\n";
        }
        $html .= '</pre>';

        return $html;

    }

    protected function renderConsole($lines)
    {
        $html = $this->ansi2Html($lines);

//        $command = "cd $path && sh $deploy_script $env";
//        exec($command, $lines);
//        $html .= $this->ansi2Html($lines);
        $template = '
        <!DOCTYPE html>
        <html>
            <body>
                <pre style="background-color: black; overflow: auto; padding: 10px 15px; font-family: monospace;"
                >%s</pre>
            </body>
        </html>
        ';
        $result = sprintf($template, $html);

        return $result;
    }

    private function log($log)
    {

        $dir = $this->getParameter('kernel.root_dir') . '/../web/';
        $filename = 'log.html';
        $file = $dir . $filename;

        ob_start();
        if (is_string($log)) {
            echo $log;
        } else {
            d($log);
        }
        $content = ob_get_clean();

        file_put_contents($file, $content, FILE_APPEND | LOCK_EX);

    }

    private function runCommand($command)
    {
        exec($command, $output);

        return $output;
    }

}
