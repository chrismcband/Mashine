<?php
/**
 * src/controllers/plugins.php
 *
 * PHP version 5
 *
 * @category   PHPFrame_Applications
 * @package    Mashine
 * @subpackage Controllers
 * @author     Lupo Montero <lupo@e-noise.com>
 * @copyright  2010 E-NOISE.COM LIMITED
 * @license    http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link       http://github.com/E-NOISE/Mashine
 */

/**
 * Plugins controller.
 *
 * @category PHPFrame_Applications
 * @package  Mashine
 * @author   Lupo Montero <lupo@e-noise.com>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://github.com/E-NOISE/Mashine
 * @since    1.0
 */
class PluginsController extends PHPFrame_ActionController
{
    /**
     * Constructor.
     *
     * @param PHPFrame_Application $app Reference to application object.
     *
     * @return void
     * @since  1.0
     */
    public function __construct(PHPFrame_Application $app)
    {
        parent::__construct($app, "index");

        $this->ensureIsStaff();
    }

    public function index()
    {
        $content = $this->request()->param("_content_active");
        $view    = $this->view("admin/plugins/index");

        $view->addData("title", $content->title());
        $view->addData("plugins", $this->app()->plugins());

        $this->response()->title($content->title());
        $this->response()->body($view);
    }

    public function options($id=null)
    {
        $id = filter_var($id, FILTER_VALIDATE_INT);
        if ($id === false) {
            $this->setRedirect($this->config()->get("base_url")."admin/plugins");
            return;
        }

        $plugin = null;
        foreach ($this->app()->plugins() as $item) {
            if ($item->id() == $id) {
                $plugin = $item;
            }
        }

        if (!$plugin instanceof PHPFrame_PluginInfo) {
            $this->response()->statusCode(400);
            $this->raiseError("No plugin found with that id.");
            return;
        }

        $plugin_class = $plugin->name();
        $plugin = new $plugin_class($this->app());

        $content = $this->request()->param("_content_active");
        $view    = $this->view("admin/plugins/options");

        $view->addData("title", $content->title());
        $view->addData("plugin", $plugin);

        $this->response()->title($content->title());
        $this->response()->body($view);
    }

    public function save_options()
    {
        $options = $this->request()->param("_options");

        try {
            foreach ($this->request()->params() as $key=>$value) {
                if (preg_match("/^options_([a-zA-Z0-9_]+)$/", $key, $matches)) {
                    $options[$matches[1]] = $value;
                }
            }

            $this->notifySuccess("Options saved!");

        } catch (Exception $e) {
            $msg = "An error occurred when saving options.";
            $this->raiseError($msg);
        }

        $base_url = $this->config()->get("base_url");
        $ret_url  = $this->request()->param("ret_url", $base_url."admin/plugins");

        $this->setRedirect($ret_url);
    }

    public function enable($id)
    {
        $this->_setEnabled($id, true);
    }

    public function disable($id)
    {
        if ($id == 1) {
            throw new Exception("Mashine plugin can not be disabled!");
        }

        $this->_setEnabled($id, false);
    }

    private function _setEnabled($id, $bool)
    {
        try {
            $api_controller = new PluginsApiController($this->app(), true);
            $api_controller->format("php");
            $api_controller->returnInternalPHP(true);
            $plugin = $api_controller->post($id, null, $bool);

            if (!$plugin instanceof PHPFrame_PluginInfo
                || $plugin->enabled() != ((bool) $bool)
            ) {
                $msg = "An error occurred while saving the plugin info.";
                throw new Exception($msg);
            }

            $action = ($bool) ? "enabled" : "disabled";
            $this->notifySuccess("Plugin ".$action." successfully!");

        } catch (Exception $e) {
            $this->raiseError($e->getMessage());
            return;
        }

        $this->setRedirect($this->config()->get("base_url")."admin/plugins");
    }
}
