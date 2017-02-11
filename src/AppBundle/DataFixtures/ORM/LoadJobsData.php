<?php
use AppBundle\Entity\Job;
use Doctrine\Common\DataFixtures\FixtureInterface;

require_once __DIR__.'/../../../../vendor/autoload.php';
use Symfony\Component\Finder\Finder;
use Symfony\Component\Yaml\Yaml;

class LoadJobsData implements FixtureInterface
{

    /**
     * Load data fixtures with the passed EntityManager
     *
     * @param \Doctrine\Common\Persistence\ObjectManager $manager
     */
    public function load(\Doctrine\Common\Persistence\ObjectManager $manager)
    {
        $job_ot = new Job();
        $job_ot->setTitle("Отделочные работы");
        $job_ot->setShortDescription("Качественно и в установленный срок проведем работы по мелкому и капитальному ремонту помещений (квартир, частных домов, гаражей, промышленных складов, ангаров и т.д.):");
        $job_ot->setDescription("Все хотят жить в комфортных условиях и бедный и богатый, мы даем эту возможность каждому. Сделаем ремонтные работы на высоком уровне не смотря на Ваше материальное положение. Подберем идеальный для Вас вариант работ, чтобы Вы остались довольны. Так же наш мастер выполнит любые мелкие работы (оплата почасовая). Приемлемые цены, порядочный подход к договоренностям, за качество отвечаем.");
        $manager->persist($job_ot);

        $job_el = new Job();
        $job_el->setTitle("Электромонтаж");
        $job_el->setShortDescription("монтаж проводки, подключение к сети общего питания, установка электрических приборов в помещениях (квартир, частных домов, гаражей, промышленных складов, ангаров и т.д.)");
        $job_el->setDescription("Как не крути, но электричество напрямую влияет на нас. Находясь в городе, без электричества мы бы не смогли зарядить телефон, воспользоваться интернетом, поддерживать продукты в надлежащих температурных условиях, заправиться на автозаправке, смотреть ТВ, готовить в квартире, использовать электро и бытовые приборы. К слову говоря, неисправная электропроводка может привести к возгоранию, а это в свою очередь может повлечь кучу последствий. Как правило проводку делают раз и на долго. Многие дома еще не соответствуют нынешним стандартам и остались алюминиевые которые уже давно пора поменять на медь, но в виду ряда разных причин еще этого не сделали. Мы с профессиональным подходом выполним эти задачи.");
        $manager->persist($job_el);

        $job_cam = new Job();
        $job_cam->setTitle("Видеонаблюдение");
        $job_cam->setShortDescription("монтаж, подключение, настройка, наладка систем видеонаблюдения в помещениях (квартир, частных домов, гаражей, промышленных складов, ангаров и т.д.)");
        $job_cam->setDescription("Одной из важнейших частей нашей жизни, является безопасность. Одно из направлений безопасности является видеонаблюдение. Это стало очень актуально, особенно тогда, когда уровень безработицы растет, а цены в магазинах только дорожают. Важно понимать что мы не всегда можем сами ограничить доступ к своему имуществу, не важно будь то вещи в квартире или на работе в офисе или на производстве и никто не застрахован от неприятных моментов связанных с потерей или кражей имущества. Видеонаблюдение как раз дает нам возможность выявить сам факт нарушения, а чаще всего и определить лиц причастных к данной ситуации. Если Вы хотите быть в курсе событий того, что происходит у Вас в квартире, на детской площадке во дворе, на даче, в офисе, на производстве, на стоянке то видеонаблюдение это Ваш выбор и не стоит сомневаться. Мы качественно и в установленные сроки выполним работы по монтажу, подключению и настройке систем видеонаблюдения в любых объемах.");
        $manager->persist($job_cam);

        $job_sec = new Job();
        $job_sec->setTitle("Системы контроля доступа");
        $job_sec->setShortDescription("магнитные замки, домофоны, турникеты, GSM сигнализации");
        $job_sec->setDescription("Техническая защита вашего имущества подразумевает под собой ограничение доступа на объекты, своевременной получение информации о том или ином событие и своевременное принятие мер по решению возникших обстоятельств, влекущих вред, кражу имущества или утечке важной информации с устройств хранения данных на объектах защиты. Если Вы серьезно задумались о собственной безопасности, тогда Вы наш клиент. Мы поможем Вам разработать проект безопасности вашего объекта, приобретём необходимые технические устройства со скидкой и установим все в срок.");
        $manager->persist($job_sec);

        $unit_et = new \AppBundle\Entity\Unit();
        $unit_et->setTitle("шт");
        $manager->persist($unit_et);
        $unit_m = new \AppBundle\Entity\Unit();
        $unit_m->setTitle("м");
        $manager->persist($unit_m);
        $unit_m2 = new \AppBundle\Entity\Unit();
        $unit_m2->setTitle("м2");
        $manager->persist($unit_m2);

        $data = Yaml::parse(file_get_contents(__DIR__."/products_electro.yml"));
        foreach ($data as $item) {
            $category = new \AppBundle\Entity\ProductCategory();
            $category->setTitle($item['title']);
            $category->setJob($job_el);

            $manager->persist($category);

            foreach ($item['items'] as $product) {
                $prod = new \AppBundle\Entity\Product();
                $prod->setTitle($product['title']);
                $prod->setPrice($product['price']);
                $prod->setUnit($product['unit'] ?? "");
                $prod->setProductCategory($category);

                $manager->persist($prod);
            }
        }

        $data = Yaml::parse(file_get_contents(__DIR__."/products_montage.yml"));
        foreach ($data as $item) {
            $category = new \AppBundle\Entity\ProductCategory();
            $category->setTitle($item['title']);
            $category->setJob($job_cam);

            $manager->persist($category);

            foreach ($item['items'] as $product) {
                $prod = new \AppBundle\Entity\Product();
                $prod->setTitle($product['title']);
                $prod->setPrice($product['price']);
                $prod->setUnit($product['unit'] ?? "");
                $prod->setProductCategory($category);

                $manager->persist($prod);
            }
        }

        $data = Yaml::parse(file_get_contents(__DIR__."/products_otdelka.yml"));
        foreach ($data as $item) {
            $category = new \AppBundle\Entity\ProductCategory();
            $category->setTitle($item['title']);
            $category->setJob($job_ot);

            $manager->persist($category);

            foreach ($item['items'] as $product) {
                $prod = new \AppBundle\Entity\Product();
                $prod->setTitle($product['title']);
                $prod->setPriceMin($product['price_from'] ?: $product['price_to']);
                $prod->setPriceMax($product['price_to'] ?: $product['price_from']);
                $prod->setUnit($product['unit'] ?? "");
                $prod->setProductCategory($category);

                $manager->persist($prod);
            }
        }

        $manager->flush();
    }
}