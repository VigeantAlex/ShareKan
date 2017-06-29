<?php

namespace AppBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\DependencyInjection\ContainerAwareInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use WCS\ShareKanBundle\Entity\Playlist;
use WCS\ShareKanBundle\Entity\Share;
use WCS\ShareKanBundle\Entity\Tag;
use WCS\ShareKanBundle\Entity\User;

class LoadPlaylistData implements FixtureInterface, ContainerAwareInterface
{
    private $container;
    public function setContainer(ContainerInterface $container = null)
    {
       $this->container = $container;
    }

    public function load(ObjectManager $manager)
    {

        $user = new User();
        $user->setUsername('admin');
        $user->setEmail('admin@kek.fr');
        $user->setPassword('test');
        $encoder = $this->container->get('security.password_encoder');

        $password = $encoder->encodePassword($user, "test");
        $user->setPassword($password);

        // 4) save the User!
        $manager->persist($user);
        $em = $manager;
        $playlist = new Playlist();
        $playlist->setNom('Regis Roberrrrrrrr');
        $jsondata = array(
            "shares" => ['https://www.youtube.com/watch?v=9VeyfvJ3iZs'],
            "user"=>1,
            "public" => true,
            "tags" => ["pop","rock","couille"]
        );
        foreach($jsondata["shares"] as $shareurl){
            $share = new Share();
            $share->setUrl($shareurl);
            $share->setPlaylist($playlist);
            $playlist->addShare($share);
            $em->persist($share);
        }
        $playlist->setCreator($user);

        $playlist->setDatetime(new \DateTime());
        $playlist->setPublic($jsondata["public"]);

        foreach($jsondata["tags"] as $tagsname){
            $tag = $em->getRepository('WCSShareKanBundle:Tag')->findOneByTag($tagsname);
            if ($tag === null){
                $tag = new Tag();
                $tag->setTag($tagsname);
                $em->persist($tag);
            }
            $tag->addPlaylist($playlist);
            $playlist->addTag($tag);
        }
        $playlist->setVote(0);
        $em->persist($playlist);
        $em->flush();
    }
}