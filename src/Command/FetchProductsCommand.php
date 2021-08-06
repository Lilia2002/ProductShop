<?php


namespace App\Command;

use App\Entity\Category;
use App\Entity\Product;
use App\Form\Type\ProductType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpClient\HttpClient;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;


class FetchProductsCommand extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'app:fetch-products';

    /** @var HttpClientInterface  */
    private $httpClient;

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var MailerInterface  */
    private $mailer;

    public function __construct(HttpClientInterface $httpClient, EntityManagerInterface $entityManager, MailerInterface $mailer)
    {
        $this->httpClient    = $httpClient;
        $this->entityManager = $entityManager;
        $this->mailer        = $mailer;

        parent::__construct();
    }


    protected function configure(): void
    {
        // ...
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $response = $this->httpClient->request('GET', 'https://fakestoreapi.com/products');

        $array = json_decode($response->getContent(), true);

        foreach ($array as $value) {
            $product = $this->entityManager->getRepository(Product::class)->findOneBy(['name' => $value['title']]);
            $category = $this->entityManager->getRepository(Category::class)->findOneBy(['name' => $value['category']]);

            if (!$product) {
                $product = new Product();

                if (!$category) {
                    $category = new Category();
                    $category->setName($value['category']);
                }

                $product->setDescription($value['description']);
                $product->setPrice($value['price'] * 100);
                $product->setName($value['title']);
                $product->setImg($value['image']);
                $product->setQuantity(rand(9, 19));
                $product->setCategory($category);

                $this->entityManager->persist($product);
                $this->entityManager->flush();
            }
        }


        // отправить письмо об успешном экспорте себе на рабочую почту
        $email = (new Email())
            ->from('devzimalab@gmail.com')
            ->to('liliya.p@zimalab.com')
            ->subject('Экспорт данных')
            ->html('<p>Данные успешно добавлены в базу!</p>');

        $this->mailer->send($email);

        return Command::SUCCESS;
    }
}
