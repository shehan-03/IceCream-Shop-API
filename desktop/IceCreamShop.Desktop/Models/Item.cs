namespace IceCreamShop.Desktop.Models;

public class Item
{
    public int Id { get; set; }
    public int CategoryId { get; set; }
    public string Name { get; set; } = "";
    public string? Description { get; set; }
    public decimal Price { get; set; }
    public int Quantity { get; set; }
    public Category? Category { get; set; }
}