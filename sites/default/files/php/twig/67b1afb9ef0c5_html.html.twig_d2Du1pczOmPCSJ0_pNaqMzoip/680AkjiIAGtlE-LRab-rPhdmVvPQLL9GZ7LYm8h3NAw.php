<?php

use Twig\Environment;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Extension\CoreExtension;
use Twig\Extension\SandboxExtension;
use Twig\Markup;
use Twig\Sandbox\SecurityError;
use Twig\Sandbox\SecurityNotAllowedTagError;
use Twig\Sandbox\SecurityNotAllowedFilterError;
use Twig\Sandbox\SecurityNotAllowedFunctionError;
use Twig\Source;
use Twig\Template;
use Twig\TemplateWrapper;

/* themes/custom/templates/page/html.html.twig */
class __TwigTemplate_33600092acf728504d98540ea75c2a56 extends Template
{
    private Source $source;
    /**
     * @var array<string, Template>
     */
    private array $macros = [];

    public function __construct(Environment $env)
    {
        parent::__construct($env);

        $this->source = $this->getSourceContext();

        $this->parent = false;

        $this->blocks = [
        ];
        $this->sandbox = $this->extensions[SandboxExtension::class];
        $this->checkSecurity();
    }

    protected function doDisplay(array $context, array $blocks = []): iterable
    {
        $macros = $this->macros;
        // line 26
        yield "<!DOCTYPE html>
<html";
        // line 27
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["html_attributes"] ?? null), "html", null, true);
        yield ">
  <head> 
    <head-placeholder token=\"";
        // line 29
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["placeholder_token"] ?? null));
        yield "\">
    <title>";
        // line 30
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar($this->extensions['Drupal\Core\Template\TwigExtension']->safeJoin($this->env, ($context["head_title"] ?? null), " | "));
        yield "</title>
    <css-placeholder token=\"";
        // line 31
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["placeholder_token"] ?? null));
        yield "\">

    <js-placeholder token=\"";
        // line 33
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["placeholder_token"] ?? null));
        yield "\">

    <link rel=\"stylesheet\" href=\"";
        // line 35
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["theme_path"] ?? null), "html", null, true);
        yield "/css/custom.css\" media=\"screen\" />
    <link rel=\"stylesheet\" href=\"";
        // line 36
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["theme_path"] ?? null), "html", null, true);
        yield "/css/update.css\" media=\"screen\" />

    ";
        // line 38
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["links_google_fonts"] ?? null));
        yield "

    ";
        // line 40
        if (($context["customize_css"] ?? null)) {
            // line 41
            yield "      <style type=\"text/css\">
        ";
            // line 42
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["customize_css"] ?? null));
            yield "
      </style>
    ";
        }
        // line 45
        yield "
    ";
        // line 46
        if (($context["customize_styles"] ?? null)) {
            // line 47
            yield "      ";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["customize_styles"] ?? null));
            yield "
    ";
        }
        // line 49
        yield "
  </head>

  ";
        // line 52
        $context["body_classes"] = [((        // line 53
($context["logged_in"] ?? null)) ? ("logged-in") : ("")), (( !        // line 54
($context["root_path"] ?? null)) ? ("frontpage") : (("path-" . \Drupal\Component\Utility\Html::getClass(($context["root_path"] ?? null))))), ((        // line 55
($context["node_type"] ?? null)) ? (("node--type-" . \Drupal\Component\Utility\Html::getClass(($context["node_type"] ?? null)))) : ("")), ((        // line 56
($context["node_id"] ?? null)) ? (("node-" . \Drupal\Component\Utility\Html::getClass(($context["node_id"] ?? null)))) : (""))];
        // line 59
        yield "
  <body";
        // line 60
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["attributes"] ?? null), "addClass", [($context["body_classes"] ?? null)], "method", false, false, true, 60), "html", null, true);
        yield ">

    <a href=\"#main-content\" class=\"visually-hidden focusable\">
      ";
        // line 63
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Skip to main content"));
        yield "
    </a>
    ";
        // line 65
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["page_top"] ?? null), "html", null, true);
        yield "
    ";
        // line 66
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["page"] ?? null), "html", null, true);
        yield "
    ";
        // line 67
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["page_bottom"] ?? null), "html", null, true);
        yield "
    <js-bottom-placeholder token=\"";
        // line 68
        yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["placeholder_token"] ?? null));
        yield "\">
    
    ";
        // line 70
        if (($context["addon_template"] ?? null)) {
            // line 71
            yield "      <div class=\"permission-save-";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["save_customize_permission"] ?? null), "html", null, true);
            yield "\">
        ";
            // line 72
            yield from $this->loadTemplate(($context["addon_template"] ?? null), "themes/custom/templates/page/html.html.twig", 72)->unwrap()->yield($context);
            // line 73
            yield "      </div>  
    ";
        }
        // line 75
        yield "    
  </body>
</html>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["html_attributes", "placeholder_token", "head_title", "theme_path", "links_google_fonts", "customize_css", "customize_styles", "logged_in", "root_path", "node_type", "node_id", "attributes", "page_top", "page", "page_bottom", "addon_template", "save_customize_permission"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/custom/templates/page/html.html.twig";
    }

    /**
     * @codeCoverageIgnore
     */
    public function isTraitable(): bool
    {
        return false;
    }

    /**
     * @codeCoverageIgnore
     */
    public function getDebugInfo(): array
    {
        return array (  161 => 75,  157 => 73,  155 => 72,  150 => 71,  148 => 70,  143 => 68,  139 => 67,  135 => 66,  131 => 65,  126 => 63,  120 => 60,  117 => 59,  115 => 56,  114 => 55,  113 => 54,  112 => 53,  111 => 52,  106 => 49,  100 => 47,  98 => 46,  95 => 45,  89 => 42,  86 => 41,  84 => 40,  79 => 38,  74 => 36,  70 => 35,  65 => 33,  60 => 31,  56 => 30,  52 => 29,  47 => 27,  44 => 26,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/custom/templates/page/html.html.twig", "D:\\xampp\\htdocs\\funobotz\\themes\\custom\\templates\\page\\html.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 40, "set" => 52, "include" => 72);
        static $filters = array("escape" => 27, "raw" => 29, "safe_join" => 30, "clean_class" => 54, "t" => 63);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if', 'set', 'include'],
                ['escape', 'raw', 'safe_join', 'clean_class', 't'],
                [],
                $this->source
            );
        } catch (SecurityError $e) {
            $e->setSourceContext($this->source);

            if ($e instanceof SecurityNotAllowedTagError && isset($tags[$e->getTagName()])) {
                $e->setTemplateLine($tags[$e->getTagName()]);
            } elseif ($e instanceof SecurityNotAllowedFilterError && isset($filters[$e->getFilterName()])) {
                $e->setTemplateLine($filters[$e->getFilterName()]);
            } elseif ($e instanceof SecurityNotAllowedFunctionError && isset($functions[$e->getFunctionName()])) {
                $e->setTemplateLine($functions[$e->getFunctionName()]);
            }

            throw $e;
        }

    }
}
