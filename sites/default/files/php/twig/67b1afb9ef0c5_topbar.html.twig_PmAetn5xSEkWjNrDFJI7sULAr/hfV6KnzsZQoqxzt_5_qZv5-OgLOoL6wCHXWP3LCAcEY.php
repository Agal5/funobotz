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

/* themes/gavias_edmix/templates/page/parts/topbar.html.twig */
class __TwigTemplate_a38641e60073718a05a19d39f6412a9c extends Template
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
        // line 1
        yield "<div class=\"topbar\">
  <div class=\"container\">
    <div class=\"row\">
      
      <div class=\"topbar-left col-sm-6 col-xs-6\">
        <div class=\"social-list\">
          ";
        // line 7
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "facebook", [], "any", false, false, true, 7)) {
            // line 8
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "facebook", [], "any", false, false, true, 8), "html", null, true);
            yield "\"><i class=\"fa fa-facebook\"></i></a>
          ";
        }
        // line 9
        yield " 
          ";
        // line 10
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "twitter", [], "any", false, false, true, 10)) {
            // line 11
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "twitter", [], "any", false, false, true, 11), "html", null, true);
            yield "\"><i class=\"fa fa-twitter-square\"></i></a>
          ";
        }
        // line 12
        yield " 
          ";
        // line 13
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "skype", [], "any", false, false, true, 13)) {
            // line 14
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "skype", [], "any", false, false, true, 14), "html", null, true);
            yield "\"><i class=\"fa fa-skype\"></i></a>
          ";
        }
        // line 15
        yield " 
          ";
        // line 16
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "instagram", [], "any", false, false, true, 16)) {
            // line 17
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "instagram", [], "any", false, false, true, 17), "html", null, true);
            yield "\"><i class=\"fa fa-instagram\"></i></a>
          ";
        }
        // line 18
        yield " 
          ";
        // line 19
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "dribbble", [], "any", false, false, true, 19)) {
            // line 20
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "dribbble", [], "any", false, false, true, 20), "html", null, true);
            yield "\"><i class=\"fa fa-dribbble\"></i></a>
          ";
        }
        // line 21
        yield " 
          ";
        // line 22
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "linkedin", [], "any", false, false, true, 22)) {
            // line 23
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "linkedin", [], "any", false, false, true, 23), "html", null, true);
            yield "\"><i class=\"fa fa-linkedin-square\"></i></a>
          ";
        }
        // line 24
        yield " 
          ";
        // line 25
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "pinterest", [], "any", false, false, true, 25)) {
            // line 26
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "pinterest", [], "any", false, false, true, 26), "html", null, true);
            yield "\"><i class=\"fa fa-pinterest\"></i></a>
          ";
        }
        // line 27
        yield " 
          ";
        // line 28
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "google", [], "any", false, false, true, 28)) {
            // line 29
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "google", [], "any", false, false, true, 29), "html", null, true);
            yield "\"><i class=\"fa fa-google-plus-square\"></i></a>
          ";
        }
        // line 30
        yield " 
          ";
        // line 31
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "youtube", [], "any", false, false, true, 31)) {
            // line 32
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "youtube", [], "any", false, false, true, 32), "html", null, true);
            yield "\"><i class=\"fa fa-youtube-square\"></i></a>
          ";
        }
        // line 33
        yield " 
          ";
        // line 34
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "vimeo", [], "any", false, false, true, 34)) {
            // line 35
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "vimeo", [], "any", false, false, true, 35), "html", null, true);
            yield "\"><i class=\"fa fa-vimeo-square\"></i></a>
          ";
        }
        // line 36
        yield "  
          ";
        // line 37
        if (CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "tumblr", [], "any", false, false, true, 37)) {
            // line 38
            yield "            <a href=\"";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, CoreExtension::getAttribute($this->env, $this->source, ($context["custom_social_link"] ?? null), "tumblr", [], "any", false, false, true, 38), "html", null, true);
            yield "\"><i class=\"fa fa-tumblr-square\"></i></a>
          ";
        }
        // line 39
        yield "  
        </div>
      </div>

      <div class=\"topbar-right col-sm-6 col-xs-6\">
        
        ";
        // line 45
        if ((($context["custom_account"] ?? null) == "")) {
            // line 46
            yield "          <ul class=\"gva_topbar_menu\">
            <li><a href=\"";
            // line 47
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["login_link"] ?? null), "html", null, true);
            yield "\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Login"));
            yield "</a></li>
            <li><a href=\"";
            // line 48
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->escapeFilter($this->env, ($context["register_link"] ?? null), "html", null, true);
            yield "\">";
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(t("Register"));
            yield "</a></li>
          </ul>  
        ";
        } else {
            // line 51
            yield "          <ul class=\"gva_topbar_menu login\">
            <li>";
            // line 52
            yield $this->extensions['Drupal\Core\Template\TwigExtension']->renderVar(($context["custom_account"] ?? null));
            yield "</li>
          </ul>  
        ";
        }
        // line 54
        yield "  
      </div>

    </div>
  </div>  
</div>
";
        $this->env->getExtension('\Drupal\Core\Template\TwigExtension')
            ->checkDeprecations($context, ["custom_social_link", "custom_account", "login_link", "register_link"]);        yield from [];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getTemplateName(): string
    {
        return "themes/gavias_edmix/templates/page/parts/topbar.html.twig";
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
        return array (  206 => 54,  200 => 52,  197 => 51,  189 => 48,  183 => 47,  180 => 46,  178 => 45,  170 => 39,  164 => 38,  162 => 37,  159 => 36,  153 => 35,  151 => 34,  148 => 33,  142 => 32,  140 => 31,  137 => 30,  131 => 29,  129 => 28,  126 => 27,  120 => 26,  118 => 25,  115 => 24,  109 => 23,  107 => 22,  104 => 21,  98 => 20,  96 => 19,  93 => 18,  87 => 17,  85 => 16,  82 => 15,  76 => 14,  74 => 13,  71 => 12,  65 => 11,  63 => 10,  60 => 9,  54 => 8,  52 => 7,  44 => 1,);
    }

    public function getSourceContext(): Source
    {
        return new Source("", "themes/gavias_edmix/templates/page/parts/topbar.html.twig", "D:\\xampp\\htdocs\\funobotz\\themes\\gavias_edmix\\templates\\page\\parts\\topbar.html.twig");
    }
    
    public function checkSecurity()
    {
        static $tags = array("if" => 7);
        static $filters = array("escape" => 8, "t" => 47, "raw" => 52);
        static $functions = array();

        try {
            $this->sandbox->checkSecurity(
                ['if'],
                ['escape', 't', 'raw'],
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
