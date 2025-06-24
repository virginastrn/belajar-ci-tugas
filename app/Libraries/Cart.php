<?php

namespace App\Libraries;

class Cart
{
    protected $session;
    protected $cart = [];

    public function __construct()
    {
        $this->session = session();
        $this->cart = $this->session->get('cart') ?? [];
    }

    public function contents()
    {
        return $this->cart;
    }

    public function insert($item)
    {
        $this->cart[] = $item;
        $this->session->set('cart', $this->cart);
    }

    public function total()
    {
        return array_sum(array_column($this->cart, 'price'));
    }

    public function destroy()
    {
        $this->cart = [];
        $this->session->remove('cart');
    }

    public function update($data)
    {
        // logika update qty berdasarkan index atau id
    }

    public function remove($rowid)
    {
        unset($this->cart[$rowid]);
        $this->session->set('cart', $this->cart);
    }
}
